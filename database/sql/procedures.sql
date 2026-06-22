-- =============================================================================
-- VisitIranian Doctors Platform — Stored Procedures
-- Requires: database/sql/schema.sql executed first
-- Run: mysql -u root -p visitiranian < database/sql/procedures.sql
-- =============================================================================

USE `visitiranian`;

DELIMITER //

-- Drop existing procedures and functions
DROP PROCEDURE IF EXISTS `sp_search_doctors`//
DROP PROCEDURE IF EXISTS `sp_get_available_slots`//
DROP PROCEDURE IF EXISTS `sp_book_appointment`//
DROP PROCEDURE IF EXISTS `sp_cancel_appointment`//
DROP PROCEDURE IF EXISTS `sp_get_expiring_doctors`//
DROP PROCEDURE IF EXISTS `sp_notify_waitlist_for_slot`//
DROP PROCEDURE IF EXISTS `sp_doctor_has_slot_on_date`//
DROP PROCEDURE IF EXISTS `sp_find_patient_appointments`//
DROP PROCEDURE IF EXISTS `sp_refresh_doctor_search_text`//
DROP FUNCTION IF EXISTS `fn_normalize_persian`//
DROP FUNCTION IF EXISTS `fn_generate_tracking_code`//

-- Helper: normalize Persian/Arabic characters for search matching
CREATE FUNCTION `fn_normalize_persian`(input_text VARCHAR(500))
RETURNS VARCHAR(500)
DETERMINISTIC
NO SQL
BEGIN
  DECLARE result VARCHAR(500);
  SET result = LOWER(TRIM(COALESCE(input_text, '')));
  SET result = REPLACE(result, 'ي', 'ی');
  SET result = REPLACE(result, 'ك', 'ک');
  SET result = REPLACE(result, 'ة', 'ه');
  SET result = REPLACE(result, 'ؤ', 'و');
  SET result = REPLACE(result, 'إ', 'ا');
  SET result = REPLACE(result, 'أ', 'ا');
  SET result = REPLACE(result, 'آ', 'ا');
  SET result = REPLACE(result, '‌', ' ');
  SET result = REPLACE(result, '  ', ' ');
  RETURN result;
END//

-- Helper: generate random 8-char tracking code (uppercase alphanumeric)
CREATE FUNCTION `fn_generate_tracking_code`()
RETURNS CHAR(8)
NOT DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE chars VARCHAR(36) DEFAULT 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  DECLARE code CHAR(8) DEFAULT '';
  DECLARE i INT DEFAULT 1;
  DECLARE idx INT;
  WHILE i <= 8 DO
    SET idx = FLOOR(1 + RAND() * CHAR_LENGTH(chars));
    SET code = CONCAT(code, SUBSTRING(chars, idx, 1));
    SET i = i + 1;
  END WHILE;
  RETURN code;
END//

-- ---------------------------------------------------------------------------
-- sp_search_doctors
-- Filter by city, specialty, VIP; tokenized flexible name search
-- Stopwords: دکتر, دكتر, dr, doctor
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_search_doctors`(
  IN p_city_id       BIGINT UNSIGNED,
  IN p_specialty_id  BIGINT UNSIGNED,
  IN p_is_vip        TINYINT,
  IN p_query         VARCHAR(255),
  IN p_limit         INT,
  IN p_offset        INT
)
BEGIN
  DECLARE v_query_norm VARCHAR(500);
  DECLARE v_token VARCHAR(100);
  DECLARE v_pos INT;
  DECLARE v_rest VARCHAR(500);
  DECLARE v_token_filter TEXT DEFAULT '1=1';

  SET p_limit  = IFNULL(NULLIF(p_limit, 0), 20);
  SET p_offset = IFNULL(p_offset, 0);

  SET v_query_norm = fn_normalize_persian(p_query);
  SET v_query_norm = REPLACE(v_query_norm, 'دکتر ', ' ');
  SET v_query_norm = REPLACE(v_query_norm, 'دكتر ', ' ');
  SET v_query_norm = REPLACE(v_query_norm, 'dr ', ' ');
  SET v_query_norm = REPLACE(v_query_norm, 'doctor ', ' ');
  SET v_query_norm = TRIM(v_query_norm);

  IF v_query_norm <> '' THEN
    SET v_rest = CONCAT(v_query_norm, ' ');
    WHILE CHAR_LENGTH(TRIM(v_rest)) > 0 DO
      SET v_pos = LOCATE(' ', v_rest);
      IF v_pos = 0 THEN
        SET v_token = TRIM(v_rest);
        SET v_rest = '';
      ELSE
        SET v_token = TRIM(SUBSTRING(v_rest, 1, v_pos - 1));
        SET v_rest = SUBSTRING(v_rest, v_pos + 1);
      END IF;
      IF v_token <> '' AND v_token NOT IN ('دکتر', 'دكتر', 'dr', 'doctor') THEN
        SET v_token_filter = CONCAT(
          v_token_filter,
          ' AND fn_normalize_persian(d.search_text) LIKE ',
          QUOTE(CONCAT('%', v_token, '%'))
        );
      END IF;
    END WHILE;
  END IF;

  SET @sql = CONCAT(
    'SELECT d.id, d.name, d.slug, d.bio, d.photo_path, d.city_id, d.primary_specialty_id,',
    ' d.is_published, d.is_active, d.is_vip, d.expires_at, d.published_at,',
    ' c.name AS city_name, c.slug AS city_slug,',
    ' s.name AS primary_specialty_name, s.slug AS primary_specialty_slug',
    ' FROM doctors d',
    ' INNER JOIN cities c ON c.id = d.city_id',
    ' LEFT JOIN specialties s ON s.id = d.primary_specialty_id',
    CASE WHEN p_specialty_id IS NOT NULL THEN
      ' INNER JOIN doctor_specialty ds ON ds.doctor_id = d.id AND ds.specialty_id = ' ELSE '' END,
    CASE WHEN p_specialty_id IS NOT NULL THEN p_specialty_id ELSE '' END,
    ' WHERE d.deleted_at IS NULL',
    ' AND d.is_published = 1',
    ' AND d.is_active = 1',
    CASE WHEN p_city_id IS NOT NULL THEN CONCAT(' AND d.city_id = ', p_city_id) ELSE '' END,
    CASE WHEN p_is_vip IS NOT NULL THEN CONCAT(' AND d.is_vip = ', p_is_vip) ELSE '' END,
    CASE WHEN v_query_norm <> '' THEN CONCAT(' AND (', v_token_filter, ')') ELSE '' END,
    ' ORDER BY d.is_vip DESC, d.published_at DESC, d.name ASC',
    ' LIMIT ', p_limit, ' OFFSET ', p_offset
  );

  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END//

-- ---------------------------------------------------------------------------
-- sp_get_available_slots
-- Generate available time slots for a doctor on a given date
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_get_available_slots`(
  IN p_doctor_id BIGINT UNSIGNED,
  IN p_date      DATE
)
BEGIN
  DECLARE v_dow TINYINT;
  DECLARE v_is_closed TINYINT DEFAULT 0;
  DECLARE v_exc_start TIME;
  DECLARE v_exc_end TIME;
  DECLARE v_has_exception TINYINT DEFAULT 0;

  SET v_dow = DAYOFWEEK(p_date) - 1;

  SELECT
    COALESCE(MAX(CASE WHEN is_closed = 1 THEN 1 ELSE 0 END), 0),
    MIN(CASE WHEN is_closed = 0 THEN start_time END),
    MAX(CASE WHEN is_closed = 0 THEN end_time END),
    COUNT(*)
  INTO v_is_closed, v_exc_start, v_exc_end, v_has_exception
  FROM doctor_schedule_exceptions
  WHERE doctor_id = p_doctor_id
    AND exception_date = p_date;

  IF v_is_closed = 1 THEN
    SELECT NULL AS starts_at, NULL AS ends_at WHERE 1 = 0;
  ELSE
    WITH RECURSIVE schedule_slots AS (
      SELECT
        ds.id AS schedule_id,
        ds.slot_duration_minutes,
        ds.clinic_id,
        CASE
          WHEN v_has_exception > 0 AND v_exc_start IS NOT NULL
            THEN v_exc_start
          ELSE ds.start_time
        END AS slot_start,
        CASE
          WHEN v_has_exception > 0 AND v_exc_end IS NOT NULL
            THEN v_exc_end
          ELSE ds.end_time
        END AS schedule_end
      FROM doctor_schedules ds
      WHERE ds.doctor_id = p_doctor_id
        AND ds.day_of_week = v_dow
        AND ds.is_active = 1
        AND NOT EXISTS (
          SELECT 1 FROM doctor_schedule_exceptions ex
          WHERE ex.doctor_id = p_doctor_id
            AND ex.exception_date = p_date
            AND ex.is_closed = 1
        )
    ),
    time_series AS (
      SELECT
        schedule_id,
        slot_duration_minutes,
        clinic_id,
        schedule_end,
        TIMESTAMP(p_date, slot_start) AS starts_at,
        TIMESTAMP(p_date, slot_start) + INTERVAL slot_duration_minutes MINUTE AS ends_at,
        slot_start AS next_slot
      FROM schedule_slots
      WHERE slot_start < schedule_end

      UNION ALL

      SELECT
        ts.schedule_id,
        ts.slot_duration_minutes,
        ts.clinic_id,
        ts.schedule_end,
        TIMESTAMP(p_date, ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60))) AS starts_at,
        TIMESTAMP(p_date, ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60)))
          + INTERVAL ts.slot_duration_minutes MINUTE AS ends_at,
        ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60)) AS next_slot
      FROM time_series ts
      WHERE ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60)) < ts.schedule_end
    )
    SELECT
      ts.starts_at,
      ts.ends_at,
      ts.clinic_id,
      ts.schedule_id
    FROM time_series ts
    WHERE ts.starts_at > NOW()
      AND NOT EXISTS (
        SELECT 1 FROM appointments a
        WHERE a.doctor_id = p_doctor_id
          AND a.status = 'confirmed'
          AND a.starts_at = ts.starts_at
      )
    ORDER BY ts.starts_at;
  END IF;
END//

-- ---------------------------------------------------------------------------
-- sp_book_appointment
-- Atomic booking with row lock; returns new appointment id + tracking code
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_book_appointment`(
  IN  p_doctor_id             BIGINT UNSIGNED,
  IN  p_clinic_id             BIGINT UNSIGNED,
  IN  p_starts_at             DATETIME,
  IN  p_ends_at               DATETIME,
  IN  p_patient_name          VARCHAR(255),
  IN  p_patient_phone         VARCHAR(20),
  IN  p_patient_national_code CHAR(10),
  OUT p_appointment_id        BIGINT UNSIGNED,
  OUT p_tracking_code         CHAR(8),
  OUT p_success               TINYINT,
  OUT p_message               VARCHAR(255)
)
proc_label: BEGIN
  DECLARE v_existing INT DEFAULT 0;
  DECLARE v_code CHAR(8);
  DECLARE v_attempts INT DEFAULT 0;
  DECLARE v_doctor_active TINYINT DEFAULT 0;

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    SET p_success = 0;
    SET p_message = 'خطا در ثبت نوبت';
    SET p_appointment_id = NULL;
    SET p_tracking_code = NULL;
  END;

  SET p_success = 0;
  SET p_message = 'ناموفق';
  SET p_appointment_id = NULL;
  SET p_tracking_code = NULL;

  SELECT COUNT(*) INTO v_doctor_active
  FROM doctors
  WHERE id = p_doctor_id
    AND deleted_at IS NULL
    AND is_active = 1
    AND is_published = 1;

  IF v_doctor_active = 0 THEN
    SET p_message = 'پزشک فعال یا منتشر شده نیست';
    LEAVE proc_label;
  END IF;

  START TRANSACTION;

  SELECT COUNT(*) INTO v_existing
  FROM appointments
  WHERE doctor_id = p_doctor_id
    AND starts_at = p_starts_at
    AND status = 'confirmed'
  FOR UPDATE;

  IF v_existing > 0 THEN
    ROLLBACK;
    SET p_message = 'این زمان قبلاً رزرو شده است';
    LEAVE proc_label;
  END IF;

  code_loop: LOOP
    SET v_code = fn_generate_tracking_code();
    SET v_attempts = v_attempts + 1;
    IF NOT EXISTS (SELECT 1 FROM appointments WHERE tracking_code = v_code) THEN
      LEAVE code_loop;
    END IF;
    IF v_attempts >= 10 THEN
      ROLLBACK;
      SET p_message = 'تولید کد پیگیری ناموفق';
      LEAVE proc_label;
    END IF;
  END LOOP;

  INSERT INTO appointments (
    doctor_id, clinic_id, starts_at, ends_at,
    patient_name, patient_phone, patient_national_code,
    tracking_code, status, booked_at, created_at, updated_at
  ) VALUES (
    p_doctor_id, p_clinic_id, p_starts_at, p_ends_at,
    p_patient_name, p_patient_phone, p_patient_national_code,
    v_code, 'confirmed', NOW(), NOW(), NOW()
  );

  SET p_appointment_id = LAST_INSERT_ID();
  SET p_tracking_code = v_code;
  SET p_success = 1;
  SET p_message = 'نوبت با موفقیت ثبت شد';

  COMMIT;
END//

-- ---------------------------------------------------------------------------
-- sp_cancel_appointment
-- Cancel appointment and free the slot
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_cancel_appointment`(
  IN  p_appointment_id      BIGINT UNSIGNED,
  IN  p_cancellation_reason VARCHAR(500),
  OUT p_success             TINYINT,
  OUT p_message             VARCHAR(255),
  OUT p_doctor_id           BIGINT UNSIGNED,
  OUT p_starts_at           DATETIME
)
proc_label: BEGIN
  DECLARE v_status VARCHAR(20);

  SET p_success = 0;
  SET p_message = 'ناموفق';
  SET p_doctor_id = NULL;
  SET p_starts_at = NULL;

  SELECT status, doctor_id, starts_at
  INTO v_status, p_doctor_id, p_starts_at
  FROM appointments
  WHERE id = p_appointment_id
  FOR UPDATE;

  IF v_status IS NULL THEN
    SET p_message = 'نوبت یافت نشد';
    LEAVE proc_label;
  END IF;

  IF v_status = 'cancelled' THEN
    SET p_message = 'نوبت قبلاً لغو شده است';
    LEAVE proc_label;
  END IF;

  IF v_status IN ('completed', 'no_show') THEN
    SET p_message = 'نوبت قابل لغو نیست';
    LEAVE proc_label;
  END IF;

  UPDATE appointments
  SET status = 'cancelled',
      cancelled_at = NOW(),
      cancellation_reason = p_cancellation_reason,
      updated_at = NOW()
  WHERE id = p_appointment_id;

  SET p_success = 1;
  SET p_message = 'نوبت لغو شد';
END//

-- ---------------------------------------------------------------------------
-- sp_get_expiring_doctors
-- Doctors expiring within p_days (7, 14, or 30)
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_get_expiring_doctors`(
  IN p_days INT
)
BEGIN
  SET p_days = IFNULL(NULLIF(p_days, 0), 30);

  SELECT
    d.id,
    d.name,
    d.slug,
    d.sms_mobile,
    d.is_vip,
    d.is_active,
    d.is_published,
    d.expires_at,
    DATEDIFF(d.expires_at, CURDATE()) AS days_remaining,
    c.name AS city_name,
    s.name AS primary_specialty_name
  FROM doctors d
  INNER JOIN cities c ON c.id = d.city_id
  LEFT JOIN specialties s ON s.id = d.primary_specialty_id
  WHERE d.deleted_at IS NULL
    AND d.expires_at IS NOT NULL
    AND d.expires_at > NOW()
    AND d.expires_at <= DATE_ADD(CURDATE(), INTERVAL p_days DAY)
  ORDER BY d.expires_at ASC, d.name ASC;
END//

-- ---------------------------------------------------------------------------
-- sp_notify_waitlist_for_slot
-- Mark first waiting patient as notified; returns row for SMS dispatch
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_notify_waitlist_for_slot`(
  IN  p_doctor_id   BIGINT UNSIGNED,
  IN  p_starts_at   DATETIME,
  IN  p_hold_hours  INT,
  OUT p_waitlist_id BIGINT UNSIGNED,
  OUT p_success     TINYINT,
  OUT p_message     VARCHAR(255)
)
proc_label: BEGIN
  DECLARE v_hold INT;

  SET v_hold = IFNULL(NULLIF(p_hold_hours, 0), 2);
  SET p_waitlist_id = NULL;
  SET p_success = 0;
  SET p_message = 'لیست انتظار خالی است';

  START TRANSACTION;

  SELECT id INTO p_waitlist_id
  FROM appointment_waitlist
  WHERE doctor_id = p_doctor_id
    AND status = 'waiting'
    AND preferred_date = DATE(p_starts_at)
    AND (preferred_starts_at IS NULL OR preferred_starts_at = p_starts_at)
  ORDER BY created_at ASC
  LIMIT 1
  FOR UPDATE;

  IF p_waitlist_id IS NULL THEN
    ROLLBACK;
    LEAVE proc_label;
  END IF;

  UPDATE appointment_waitlist
  SET status = 'notified',
      notified_at = NOW(),
      expires_at = DATE_ADD(NOW(), INTERVAL v_hold HOUR),
      updated_at = NOW()
  WHERE id = p_waitlist_id;

  COMMIT;

  SELECT
    w.id,
    w.doctor_id,
    w.patient_name,
    w.patient_phone,
    w.patient_national_code,
    w.preferred_date,
    w.preferred_starts_at,
    w.notified_at,
    w.expires_at,
    d.name AS doctor_name,
    d.slug AS doctor_slug
  FROM appointment_waitlist w
  INNER JOIN doctors d ON d.id = w.doctor_id
  WHERE w.id = p_waitlist_id;

  SET p_success = 1;
  SET p_message = 'بیمار لیست انتظار مطلع شد';
END//

-- ---------------------------------------------------------------------------
-- sp_doctor_has_slot_on_date
-- Returns 1 if doctor has at least one available slot on date
-- Used for "نوبت خالی امروز/فردا" badge
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_doctor_has_slot_on_date`(
  IN  p_doctor_id BIGINT UNSIGNED,
  IN  p_date      DATE,
  OUT p_has_slot  TINYINT
)
BEGIN
  DECLARE v_dow TINYINT;
  DECLARE v_is_closed TINYINT DEFAULT 0;
  DECLARE v_exc_start TIME;
  DECLARE v_exc_end TIME;
  DECLARE v_has_exception TINYINT DEFAULT 0;
  DECLARE v_count INT DEFAULT 0;

  SET v_dow = DAYOFWEEK(p_date) - 1;
  SET p_has_slot = 0;

  SELECT
    COALESCE(MAX(CASE WHEN is_closed = 1 THEN 1 ELSE 0 END), 0),
    MIN(CASE WHEN is_closed = 0 THEN start_time END),
    MAX(CASE WHEN is_closed = 0 THEN end_time END),
    COUNT(*)
  INTO v_is_closed, v_exc_start, v_exc_end, v_has_exception
  FROM doctor_schedule_exceptions
  WHERE doctor_id = p_doctor_id
    AND exception_date = p_date;

  IF v_is_closed = 0 THEN
    SELECT COUNT(*) INTO v_count
    FROM (
      WITH RECURSIVE schedule_slots AS (
        SELECT
          ds.id AS schedule_id,
          ds.slot_duration_minutes,
          CASE
            WHEN v_has_exception > 0 AND v_exc_start IS NOT NULL THEN v_exc_start
            ELSE ds.start_time
          END AS slot_start,
          CASE
            WHEN v_has_exception > 0 AND v_exc_end IS NOT NULL THEN v_exc_end
            ELSE ds.end_time
          END AS schedule_end
        FROM doctor_schedules ds
        WHERE ds.doctor_id = p_doctor_id
          AND ds.day_of_week = v_dow
          AND ds.is_active = 1
      ),
      time_series AS (
        SELECT
          slot_duration_minutes,
          schedule_end,
          TIMESTAMP(p_date, slot_start) AS starts_at,
          slot_start AS next_slot
        FROM schedule_slots
        WHERE slot_start < schedule_end

        UNION ALL

        SELECT
          ts.slot_duration_minutes,
          ts.schedule_end,
          TIMESTAMP(p_date, ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60))) AS starts_at,
          ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60)) AS next_slot
        FROM time_series ts
        WHERE ADDTIME(ts.next_slot, SEC_TO_TIME(ts.slot_duration_minutes * 60)) < ts.schedule_end
      )
      SELECT ts.starts_at
      FROM time_series ts
      WHERE ts.starts_at > NOW()
        AND NOT EXISTS (
          SELECT 1 FROM appointments a
          WHERE a.doctor_id = p_doctor_id
            AND a.status = 'confirmed'
            AND a.starts_at = ts.starts_at
        )
      LIMIT 1
    ) AS available;
  END IF;

  SET p_has_slot = IF(v_count > 0, 1, 0);
END//

-- ---------------------------------------------------------------------------
-- sp_find_patient_appointments
-- Lookup active/upcoming appointments by phone + national code
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_find_patient_appointments`(
  IN p_patient_phone         VARCHAR(20),
  IN p_patient_national_code CHAR(10)
)
BEGIN
  SELECT
    a.id,
    a.tracking_code,
    a.starts_at,
    a.ends_at,
    a.status,
    a.booked_at,
    a.patient_name,
    a.patient_phone,
    a.patient_national_code,
    d.id AS doctor_id,
    d.name AS doctor_name,
    d.slug AS doctor_slug,
    c.name AS city_name,
    s.name AS primary_specialty_name
  FROM appointments a
  INNER JOIN doctors d ON d.id = a.doctor_id
  INNER JOIN cities c ON c.id = d.city_id
  LEFT JOIN specialties s ON s.id = d.primary_specialty_id
  WHERE a.patient_phone = p_patient_phone
    AND a.patient_national_code = p_patient_national_code
    AND a.status = 'confirmed'
    AND a.starts_at >= NOW()
  ORDER BY a.starts_at ASC;
END//

-- ---------------------------------------------------------------------------
-- sp_refresh_doctor_search_text
-- Rebuild search_text and name_normalized for one doctor
-- ---------------------------------------------------------------------------
CREATE PROCEDURE `sp_refresh_doctor_search_text`(
  IN p_doctor_id BIGINT UNSIGNED
)
BEGIN
  DECLARE v_name VARCHAR(255);
  DECLARE v_city VARCHAR(100);
  DECLARE v_specialties TEXT;
  DECLARE v_search TEXT;

  SELECT d.name, c.name
  INTO v_name, v_city
  FROM doctors d
  INNER JOIN cities c ON c.id = d.city_id
  WHERE d.id = p_doctor_id;

  SELECT GROUP_CONCAT(s.name ORDER BY ds.is_primary DESC, s.name SEPARATOR ' ')
  INTO v_specialties
  FROM doctor_specialty ds
  INNER JOIN specialties s ON s.id = ds.specialty_id
  WHERE ds.doctor_id = p_doctor_id;

  SET v_search = CONCAT_WS(' ', v_name, v_specialties, v_city);
  SET v_search = fn_normalize_persian(v_search);

  UPDATE doctors
  SET name_normalized = fn_normalize_persian(v_name),
      search_text = v_search,
      updated_at = NOW()
  WHERE id = p_doctor_id;

  SELECT id, name, name_normalized, search_text
  FROM doctors
  WHERE id = p_doctor_id;
END//

DELIMITER ;

-- =============================================================================
-- End of procedures.sql
-- =============================================================================

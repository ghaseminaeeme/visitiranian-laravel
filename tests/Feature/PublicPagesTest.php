<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_doctors_index_loads(): void
    {
        $this->get('/doctors')->assertOk();
    }

    public function test_peygiri_page_loads(): void
    {
        $this->get('/peygiri')->assertOk();
    }

    public function test_robots_txt_loads(): void
    {
        $this->get('/robots.txt')->assertOk();
    }

    public function test_guide_pages_exist_after_seed(): void
    {
        $this->seed();

        $this->get('/p/rahnama-rzerv')->assertOk();
        $this->get('/p/rahnama-pezeshk')->assertOk();
    }
}

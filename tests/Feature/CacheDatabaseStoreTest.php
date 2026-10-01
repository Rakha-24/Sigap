<?php

namespace Tests\Feature;

use App\Cache\NeonSafeDatabaseStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheDatabaseStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_cache_store_terdaftar_sebagai_store_neon_safe(): void
    {
        $this->assertInstanceOf(
            NeonSafeDatabaseStore::class,
            Cache::store('database')->getStore()
        );
    }

    public function test_database_cache_put_get_increment_decrement(): void
    {
        Cache::store('database')->flush();

        $this->assertTrue(Cache::store('database')->put('hit', 1, 60));
        $this->assertSame(1, Cache::store('database')->get('hit'));

        $this->assertSame(2, Cache::store('database')->increment('hit'));
        $this->assertSame(3, Cache::store('database')->increment('hit'));

        $this->assertSame(2, Cache::store('database')->decrement('hit'));
        $this->assertFalse(Cache::store('database')->increment('missing_key'));
    }

    public function test_database_cache_mematuhi_ttl(): void
    {
        Cache::store('database')->put('temp', 'value-123', 1);

        $this->assertSame('value-123', Cache::store('database')->get('temp'));

        $this->travel(2)->seconds();

        $this->assertNull(Cache::store('database')->get('temp'));
    }

    public function test_database_cache_forget(): void
    {
        Cache::store('database')->put('foo', 'bar', 60);

        $this->assertSame('bar', Cache::store('database')->get('foo'));

        Cache::store('database')->forget('foo');

        $this->assertNull(Cache::store('database')->get('foo'));
    }
}

# Laravel Mosaic

[Mosaic](https://www.mosaic.net.tr) Public API için Laravel istemci kütüphanesi. Kurumların Mosaic tarafından toplanan sosyal medya verilerini (takipçi sayıları, geçmiş trend, paylaşımlar, özet metrikler) kendi Laravel uygulamalarına **salt-okunur** olarak çekmesini sağlar.

- ✅ Laravel **5.5+** (8 / 9 / 10 / 11 / 12 dahil)
- ✅ PHP **7.0+**
- ✅ Guzzle 6 / 7
- ✅ Otomatik paket keşfi (auto-discovery)

---

## Kurulum

Paket henüz Packagist'te yayınlanmadığı için, projenizin `composer.json` dosyasına VCS deposu ekleyin:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/bmericc/laravel-mosaic"
    }
]
```

Ardından:

```bash
composer require bahricanli/laravel-mosaic
```

### Yapılandırma

`.env` dosyanıza kuruma özel bilgileri ekleyin:

```env
MOSAIC_BASE_URL=https://www.mosaic.net.tr
MOSAIC_API_KEY=msk_xxxxxxxxxxxxxxxx
```

API key'i Mosaic panelinden **Kurumlar → (kurum) → Düzenle → Public API** bölümünden üretebilirsiniz.

İsterseniz config dosyasını publish edin:

```bash
php artisan vendor:publish --tag=mosaic-config
```

### Laravel 5.5 — manuel kayıt (auto-discovery kapalıysa)

`config/app.php`:

```php
'providers' => [
    BahriCanli\Mosaic\MosaicServiceProvider::class,
],

'aliases' => [
    'Mosaic' => BahriCanli\Mosaic\Facades\Mosaic::class,
],
```

---

## Kullanım

### Facade ile

```php
use Mosaic;

$overview = Mosaic::overview();
echo $overview['summary']['total_followers'];

$followers = Mosaic::followers();
$history   = Mosaic::followersHistory('instagram', 30);
$posts     = Mosaic::posts('instagram', 20);
```

### Dependency injection ile

```php
use BahriCanli\Mosaic\MosaicClient;

public function dashboard(MosaicClient $mosaic)
{
    return view('dashboard', [
        'overview' => $mosaic->overview(),
    ]);
}
```

### Birden fazla kurum / runtime key

```php
$client = new MosaicClient('https://www.mosaic.net.tr', 'msk_xxx');
$data   = $client->overview();

// veya mevcut client'ın key'ini değiştir
$other = app('mosaic')->withApiKey('msk_yyy')->overview();
```

---

## Metotlar

| Metot | Açıklama |
|---|---|
| `overview()` | Kurum bilgisi + her platformun güncel takipçisi + birleşik özet (toplam, 7 günlük büyüme) |
| `followers()` | Her platformun en güncel takipçi sayısı |
| `followersHistory($platform = null, $days = 30)` | Tarih bazlı takipçi serisi (grafik). `days` en fazla 365. |
| `posts($platform = null, $limit = 20)` | Postlar + etkileşim metrikleri. `limit` en fazla 50. |

Tüm metotlar API yanıtını ilişkisel dizi (`array`) olarak döner. Hata durumunda `BahriCanli\Mosaic\Exceptions\MosaicException` fırlatılır.

### Örnek `overview()` yanıtı

```json
{
  "organization": { "name": "Radyo C", "slug": "radyo-c" },
  "summary": { "total_followers": 123487, "growth_7d": 540, "growth_7d_percent": 0.44 },
  "platforms": [
    { "platform": "instagram", "platform_label": "Instagram", "followers": 22303 }
  ],
  "updated_at": "2026-06-19"
}
```

Tam API dokümantasyonu: <https://www.mosaic.net.tr/api-docs>

---

## Lisans

MIT

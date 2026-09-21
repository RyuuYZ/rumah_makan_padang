<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Services\WebpUploadService;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    private function formatFotoUrl(?string $foto): ?string
    {
        if (empty($foto)) {
            return null;
        }

        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
            return $foto;
        }

        return url($foto);
    }

    public function index(Request $request)
    {
        $query = MenuItem::query()
            ->where('is_active', true)
            ->with('category')
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)]);

        if ($request->has('kategori') && $request->kategori !== 'all') {
            $slug = $request->kategori;
            $query->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        if ($request->has('branch_id')) {
            $branchId = $request->branch_id;
            $menuItems = $query->with(['branchPrices' => function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            }])->get();

            return response()->json([
                'success' => true,
                'data' => $menuItems->map(function ($item) {
                    $price = $item->branchPrices->first();
                    $hargaVal = $price ? (int) $price->harga : 25000;
                    $fotoUrl = $this->formatFotoUrl($item->foto);

                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'kategori' => $item->kategori,
                        'deskripsi' => $item->deskripsi,
                        'foto' => $fotoUrl,
                        'foto_path' => $item->foto,
                        'foto_format' => str_ends_with(strtolower((string) $item->foto), '.webp') ? 'webp' : 'image',
                        'badge' => $item->badge,
                        'rating' => (float) ($item->rating ?? 4.8),
                        'review_count' => (int) ($item->reviews_count ?? 0),
                        'harga' => $hargaVal,
                        'harga_display' => 'Rp '.number_format($hargaVal, 0, ',', '.'),
                    ];
                }),
            ]);
        }

        // Without price info
        $menuItems = $query->get();

        return response()->json([
            'success' => true,
            'data' => $menuItems->map(fn ($item) => [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->kategori,
                'deskripsi' => $item->deskripsi,
                'foto' => $this->formatFotoUrl($item->foto),
                'foto_path' => $item->foto,
                'foto_format' => str_ends_with(strtolower((string) $item->foto), '.webp') ? 'webp' : 'image',
                'badge' => $item->badge,
                'rating' => (float) ($item->rating ?? 4.8),
                'review_count' => (int) ($item->reviews_count ?? 0),
            ]),
        ]);
    }

    public function show(string $id)
    {
        $menuItem = MenuItem::with('branchPrices.branch')
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->findOrFail($id);

        $data = $menuItem->toArray();
        $data['rating'] = (float) ($menuItem->rating ?? 4.8);
        $data['review_count'] = (int) ($menuItem->reviews_count ?? 0);
        $data['foto_url'] = $this->formatFotoUrl($menuItem->foto);
        $data['foto_format'] = str_ends_with(strtolower((string) $menuItem->foto), '.webp') ? 'webp' : 'image';

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function uploadImage(Request $request, WebpUploadService $webpUploadService)
    {
        $request->validate([
            'image' => 'required|image|mimes:webp,png,jpg,jpeg|max:2048',
        ]);

        $path = $webpUploadService->uploadAndConvertToWebp($request->file('image'), 'menu');

        return response()->json([
            'success' => true,
            'message' => 'Image successfully uploaded and converted to WebP format',
            'data' => [
                'path' => $path,
                'url' => url($path),
                'format' => 'webp',
            ],
        ]);
    }
}

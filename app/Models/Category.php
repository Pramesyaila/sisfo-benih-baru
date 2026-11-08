<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Slug kategori induk yang diizinkan memiliki 3 level (Kategori → Varietas → Kelas).
     * Kategori lain tetap maksimal 2 level (Kategori → Varietas).
     */
    public const GRADED_ROOT_SLUG = 'ayam-kub';

    public const MAX_DEPTH_DEFAULT = 2;
    public const MAX_DEPTH_GRADED = 3;

    protected $fillable = ['parent_id', 'name', 'slug', 'description'];

    /* -----------------------------------------------------------------
     | Relasi
     |-----------------------------------------------------------------*/

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /* -----------------------------------------------------------------
     | Scope
     |-----------------------------------------------------------------*/

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Node daun (tanpa anak) yang bukan root.
     * Gunakan scope ini untuk pilihan kategori pada form produk,
     * karena category_id produk harus menunjuk ke leaf (mis. Grade A).
     */
    public function scopeLeaves(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id')->whereDoesntHave('children');
    }

    /* -----------------------------------------------------------------
     | Struktur tree
     |-----------------------------------------------------------------*/

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Kedalaman node: 1 = kategori, 2 = varietas, 3 = kelas.
     */
    public function depth(): int
    {
        $depth = 1;
        $node = $this;

        while ($node->parent_id !== null && $node->parent) {
            $node = $node->parent;
            $depth++;
        }

        return $depth;
    }

    /**
     * Node root dari pohon ini (dirinya sendiri jika sudah root).
     */
    public function root(): self
    {
        $node = $this;

        while ($node->parent_id !== null && $node->parent) {
            $node = $node->parent;
        }

        return $node;
    }

    /**
     * Apakah node ini adalah kategori induk yang memakai level kelas (Ayam KUB)?
     */
    public function isGradedRoot(): bool
    {
        return $this->parent_id === null && $this->slug === self::GRADED_ROOT_SLUG;
    }

    /**
     * Kedalaman maksimum pohon tempat node ini berada.
     */
    public function maxDepth(): int
    {
        return $this->root()->isGradedRoot()
            ? self::MAX_DEPTH_GRADED
            : self::MAX_DEPTH_DEFAULT;
    }

    /**
     * Apakah node ini boleh menerima anak baru?
     */
    public function canHaveChildren(): bool
    {
        return $this->depth() < $this->maxDepth();
    }

    /**
     * ID node ini beserta seluruh turunannya (berguna untuk filter katalog).
     *
     * @return array<int>
     */
    public function descendantsAndSelfIds(): array
    {
        $ids = [$this->id];
        $current = [$this->id];

        while (! empty($current)) {
            $current = static::whereIn('parent_id', $current)->pluck('id')->all();
            $ids = array_merge($ids, $current);
        }

        return $ids;
    }

    /* -----------------------------------------------------------------
     | Accessor
     |-----------------------------------------------------------------*/

    /**
     * Total produk pada node ini + seluruh turunannya.
     * Memakai products_count bila sudah di-load lewat withCount().
     */
    public function getProductsTotalAttribute(): int
    {
        $total = (int) ($this->products_count ?? $this->products()->count());

        foreach ($this->children as $child) {
            $total += $child->products_total;
        }

        return $total;
    }

    /**
     * Jalur lengkap, mis. "Ayam KUB → Bibit Ayam → Grade A".
     */
    public function getFullPathAttribute(): string
    {
        $names = [$this->name];
        $node = $this;

        while ($node->parent_id !== null && $node->parent) {
            $node = $node->parent;
            array_unshift($names, $node->name);
        }

        return implode(' → ', $names);
    }

    /**
 * Apakah node ini kelas (level 3)?
 */
public function isGrade(): bool
{
    return $this->depth() === 3;
}

/**
 * Nama varietas: kalau node ini kelas, ambil nama induknya (varietas).
 * Kalau bukan kelas, nama node itu sendiri.
 */
public function getVarietyNameAttribute(): string
{
    return $this->isGrade() ? $this->parent->name : $this->name;
}

/**
 * Nama kelas, atau null bila produk tidak memakai kelas.
 */
public function getGradeNameAttribute(): ?string
{
    return $this->isGrade() ? $this->name : null;
}
}
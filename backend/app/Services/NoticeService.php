<?php

namespace App\Services;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Collection;

class NoticeService
{
    /**
     * Get active published notices.
     *
     * @param int $limit
     * @return Collection<int, Notice>
     */
    public function getActiveNotices(int $limit = 5): Collection
    {
        return Notice::where('is_active', true)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Create a new notice.
     */
    public function createNotice(array $data): Notice
    {
        return Notice::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'is_active' => $data['is_active'] ?? true,
            'published_at' => $data['published_at'] ?? now(),
        ]);
    }
}

<?php

namespace App\Repositories\Academic;

use App\Models\AcademicSession;

class AcademicSessionRepository
{
    public function paginate($request)
    {
        $query = AcademicSession::query()->with('school:id,name,code');

        if ($request->filled('school_id') && $request->school_id !== 'all') {
            $query->where('school_id', $request->school_id);
        }

        return $query
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);
    }

    public function create(array $data)
    {
        return AcademicSession::create($data);
    }

    public function update(AcademicSession $session,array $data)
    {
        $session->update($data);

        return $session;
    }

    public function delete(AcademicSession $session)
    {
        return $session->delete();
    }
}
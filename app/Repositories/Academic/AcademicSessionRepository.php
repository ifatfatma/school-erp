<?php

namespace App\Repositories\Academic;

use App\Models\AcademicSession;

class AcademicSessionRepository
{
    public function paginate($request)
    {
        return AcademicSession::query()

            ->when($request->search,function($q) use($request){

                $q->where('name','like','%'.$request->search.'%');

            })

            ->latest()

            ->paginate(
                $request->per_page ?? 10
            );
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
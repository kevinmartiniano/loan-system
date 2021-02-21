<?php

namespace App\Http\Controllers\API;

use App\Models\UserType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use Illuminate\Http\Response;

class UserTypeController extends Controller
{
    private UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(UserTypeRepositoryInterface $userTypeRepository)
    {
        $this->userTypeRepository = $userTypeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->userTypeRepository->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json(
            $this->userTypeRepository->create($request->all()),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->userTypeRepository->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->userTypeRepository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): void
    {
        $this->userTypeRepository->delete($id);

        return;
    }
}

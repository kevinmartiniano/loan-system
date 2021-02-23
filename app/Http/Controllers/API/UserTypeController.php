<?php

namespace App\Http\Controllers\API;

use App\Models\UserType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserTypeController extends Controller
{
    private UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(UserTypeRepositoryInterface $userTypeRepository)
    {
        $this->userTypeRepository = $userTypeRepository;
    }

    /**
     * @OA\Get(
     *   tags={"UserTypes"},
     *   path="/api/user-types/",
     *   @OA\Response(response="200", description="A user-type list.")
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        return response()->json($this->userTypeRepository->all(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *   tags={"UserTypes"},
     *   path="/api/user-types/",
     *   description="Create a UserTypes",
     *   @OA\RequestBody(
     *     @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string"),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="201",
     *     description="user-type created"
     *   )
     * )
     *
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
     * @OA\Get(
     *   tags={"UserTypes"},
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     @OA\Schema(type="integer")
     *   ),
     *   path="/api/user-types/{id}",
     *   @OA\Response(
     *     response="200",
     *     description="A user-type specified is returned."
     *   )
     * )
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        return response()->json($this->userTypeRepository->find($id), Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *   tags={"UserTypes"},
     *   path="/api/user-types/",
     *   description="Update a UserType",
     *   @OA\RequestBody(
     *     @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string"),
     *       )
     *     )
     *   ),
     *   @OA\Response(response="200", description="user updated")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response()->json($this->userTypeRepository->update($request->all(), $id), Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *   tags={"UserTypes"},
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     @OA\Schema(type="integer")
     *   ),
     *   path="/api/user-types/{id}",
     *   description="Delete a UserType specified",
     *   @OA\Response(
     *     response="200",
     *     description="user-type deleted"
     *   )
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        $this->userTypeRepository->delete($id);

        return response()->json([], Response::HTTP_OK);
    }
}

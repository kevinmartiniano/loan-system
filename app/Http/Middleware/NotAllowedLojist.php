<?php

namespace App\Http\Middleware;

use App\Models\UserType;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotAllowedLojist
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $payer = $request->input('payer');

        if(isset($payer)) {
            $user = $this->userRepository->find($payer);

            if($user->user_type->id == UserType::LOJIST) {
                return response()->json([
                    'error' => 'You are not allowed to perform this action'
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }
        }

        return $next($request);
    }
}

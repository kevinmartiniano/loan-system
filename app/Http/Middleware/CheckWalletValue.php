<?php

namespace App\Http\Middleware;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckWalletValue
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
        $value = $request->input('value');
        $payerId = $request->input('payer');

        if(isset($value) && !empty($payerId)) {
            $user = $this->userRepository->find($request->input('payer'));

            if($user->wallet->value < $value) {
                return response()->json([
                    'error' => 'Limit value exceeded'
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }
        }

        return $next($request);
    }
}

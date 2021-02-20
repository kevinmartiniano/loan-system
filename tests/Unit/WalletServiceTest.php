<?php

namespace Tests\Unit;

use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\WalletService;
use PHPUnit\Framework\TestCase;

class WalletServiceTest extends TestCase
{
    public function testCreateWalletRelatedUser(): void
    {
        $walletRepository = $this->createMock(WalletRepositoryInterface::class);
        
        $walletRepository->expects($this->once())
                            ->method('create')
                            ->willReturn(Wallet::class);
        
        $user = $this->createMock(User::class);

        $walletService = new WalletService($walletRepository);

        $walletService->createWallet($user);
    }
}

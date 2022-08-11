<?php

namespace Ultainfinity\SolanaPhpSdk\Programs;

use Ultainfinity\SolanaPhpSdk\Program;
use Ultainfinity\SolanaPhpSdk\PublicKey;
use Ultainfinity\SolanaPhpSdk\TransactionInstruction;
use Ultainfinity\SolanaPhpSdk\Util\AccountMeta;
use Ultainfinity\SolanaPhpSdk\Util\Commitment;

class SystemProgram extends Program
{
    const PROGRAM_INDEX_CREATE_ACCOUNT = 0;
    const PROGRAM_INDEX_TRANSFER = 2;

    /**
     * Public key that identifies the System program
     *
     * @return PublicKey
     */
    static function programId(): PublicKey
    {
        return new PublicKey('11111111111111111111111111111111');
    }

    /**
     * @param string $pubKey
     * @return array
     */
    public function getAccountInfo(string $pubKey): array
    {
        $response = $this->client->call('getAccountInfo', [$pubKey, ["encoding" => "jsonParsed"]])['value'];

        if (!$esponse) {
            throw new \Exception("Solana API Error: Account {$pubKey} not found", 404);
        }

        return $esponse;
    }

    /**
     * @param string $pubKey
     * @return float
     */
    public function getBalance(string $pubKey): float
    {
        $esponse = $this->client->call('getBalance', [$pubKey])['value'];

        if (!$esponse) {
            throw new \Exception("Solana API Error: Can't get balance for account {$pubKey}");
        }

        return $esponse;
    }

    /**
     * @param string $transactionSignature
     * @return array
     */
    public function getConfirmedTransaction(string $transactionSignature): array
    {
        $esponse = $this->client->call('getConfirmedTransaction', [$transactionSignature]);

        if (!$esponse) {
            throw new \Exception("Solana API Error: Can't get transaction {$transactionSignature}");
        }

        return $esponse;
    }

    /**
     * NEW: This method is only available in solana-core v1.7 or newer. Please use getConfirmedTransaction for solana-core v1.6
     *
     * @param string $transactionSignature
     * @return array
     */
    public function getTransaction(string $transactionSignature): array
    {
        $esponse = $this->client->call('getTransaction', [$transactionSignature]);

        if (!$esponse) {
            throw new \Exception("Solana API Error: Can't get transaction {$transactionSignature}");
        }

        return $esponse;
    }

    /**
     * @param Commitment|null $commitment
     * @return array
     * @throws Exceptions\GenericException|Exceptions\MethodNotFoundException|Exceptions\InvalidIdResponseException
     */
    public function getRecentBlockhash(?Commitment $commitment = null): array
    {
        $esponse = $this->client->call('getRecentBlockhash', array_filter([$commitment]))['value'];

        if (!$esponse) {
            throw new \Exception("Solana API Error: Can't get recent blockhash");
        }

        return $esponse;
    }

    /**
     * Generate a transaction instruction that transfers lamports from one account to another
     *
     * @param PublicKey $fromPubkey
     * @param PublicKey $toPublicKey
     * @param int $lamports
     * @return TransactionInstruction
     */
    static public function transfer(
        PublicKey $fromPubkey,
        PublicKey $toPublicKey,
        int $lamports
    ): TransactionInstruction
    {
        // 4 byte instruction index + 8 bytes lamports
        // look at https://www.php.net/manual/en/function.pack.php for formats.
        $data = [
            // uint32
            ...unpack("C*", pack("V", self::PROGRAM_INDEX_TRANSFER)),
            // int64
            ...unpack("C*", pack("P", $lamports)),
        ];
        $keys = [
            new AccountMeta($fromPubkey, true, true),
            new AccountMeta($toPublicKey, false, true),
        ];

        return new TransactionInstruction(
            static::programId(),
            $keys,
            $data
        );
    }

    static public function createAccount(
        PublicKey $fromPubkey,
        PublicKey $newAccountPublicKey,
        int $lamports,
        int $space,
        PublicKey $programId
    ): TransactionInstruction
    {
        // look at https://www.php.net/manual/en/function.pack.php for formats.
        $data = [
            // uint32
            ...unpack("C*", pack("V", self::PROGRAM_INDEX_CREATE_ACCOUNT)),
            // int64
            ...unpack("C*", pack("P", $lamports)),
            // int64
            ...unpack("C*", pack("P", $space)),
            //
            ...$programId->toBytes(),
        ];
        $keys = [
            new AccountMeta($fromPubkey, true, true),
            new AccountMeta($newAccountPublicKey, true, true),
        ];

        return new TransactionInstruction(
            static::programId(),
            $keys,
            $data
        );
    }
}

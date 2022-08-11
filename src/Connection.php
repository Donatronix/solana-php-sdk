<?php

namespace Ultainfinity\SolanaPhpSdk;

use Ultainfinity\SolanaPhpSdk\Util\Commitment;

class Connection extends Program
{
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
     * @param Transaction $transaction
     * @param Keypair[] $signers
     * @param array $params
     * @return array|\Illuminate\Http\Client\Response
     * @throws Exceptions\GenericException
     * @throws Exceptions\InvalidIdResponseException
     * @throws Exceptions\MethodNotFoundException
     */
    public function sendTransaction(Transaction $transaction, array $signers, array $params = [])
    {
        if (!$transaction->recentBlockhash) {
            $transaction->recentBlockhash = $this->getRecentBlockhash()['blockhash'];
        }

        $transaction->sign(...$signers);

        $rawBinaryString = $transaction->serialize(false);

        $hashString = sodium_bin2base64($rawBinaryString, SODIUM_BASE64_VARIANT_ORIGINAL);

        return $this->client->call('sendTransaction', [
            $hashString,
            [
                'encoding' => 'base64',
                'preflightCommitment' => 'confirmed',
            ],
        ]);
    }
}

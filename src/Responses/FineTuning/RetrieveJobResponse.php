<?php

declare(strict_types=1);

namespace OpenAI\Responses\FineTuning;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Contracts\ResponseHasMetaInformationContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Responses\Concerns\HasMetaInformation;
use OpenAI\Responses\Meta\MetaInformation;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id: string, object: string, model: string, created_at: int, finished_at: ?int, fine_tuned_model: ?string, hyperparameters: array{n_epochs: int|string, batch_size: int|string|null, learning_rate_multiplier: float|string|null}, organization_id: string, result_files: array<int, string>, status: string, validation_file: ?string, training_file: string, trained_tokens: ?int, error: ?array{code: string, param: ?string, message: string}}>
 */
final class RetrieveJobResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /**
     * @use ArrayAccessible<array{id: string, object: string, model: string, created_at: int, finished_at: ?int, fine_tuned_model: ?string, hyperparameters: array{n_epochs: int|string, batch_size: int|string|null, learning_rate_multiplier: float|string|null}, organization_id: string, result_files: array<int, string>, status: string, validation_file: ?string, training_file: string, trained_tokens: ?int, error: ?array{code: string, param: ?string, message: string}}>
     */
    use ArrayAccessible;

    use Fakeable;
    use HasMetaInformation;

    /**
     * @param  array<int, string>  $resultFiles
     */
    private function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly string $model,
        public readonly int $createdAt,
        public readonly ?int $finishedAt,
        public readonly ?string $fineTunedModel,
        public readonly RetrieveJobResponseHyperparameters $hyperparameters,
        public readonly string $organizationId,
        public readonly array $resultFiles,
        public readonly string $status,
        public readonly ?string $validationFile,
        public readonly string $trainingFile,
        public readonly ?int $trainedTokens,
        public readonly ?RetrieveJobResponseError $error,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id: string, object: string, model: string, created_at: int, finished_at: ?int, fine_tuned_model: ?string, hyperparameters: array{n_epochs: int|string, batch_size: int|string|null, learning_rate_multiplier: float|string|null}, organization_id: string, result_files: array<int, string>, status: string, validation_file: ?string, training_file: string, trained_tokens: ?int, error: ?array{code: string, param: ?string, message: string}}  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        // UGLY fix for OpenAI API Quirks. It is unclear, if "$attributes['error']['error']" can represent something meaningful
        $retrieveJobResponseError = null;
        if (array_key_exists('error', $attributes)
            && is_array($attributes['error'])
            && array_key_exists('code', $attributes['error'])
        ) {
            $retrieveJobResponseError = RetrieveJobResponseError::from($attributes['error']);
        } else if (array_key_exists('error', $attributes)
            && array_key_exists('error', $attributes['error'])
            && is_array($attributes['error']['error'])
            && array_key_exists('code', $attributes['error']['error'])
        ) {
            $retrieveJobResponseError = RetrieveJobResponseError::from($attributes['error']['error']);
        }

        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['model'],
            $attributes['created_at'],
            $attributes['finished_at'],
            $attributes['fine_tuned_model'],
            RetrieveJobResponseHyperparameters::from($attributes['hyperparameters']),
            $attributes['organization_id'],
            $attributes['result_files'],
            $attributes['status'],
            $attributes['validation_file'],
            $attributes['training_file'],
            $attributes['trained_tokens'],
            $retrieveJobResponseError,
            $meta,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'model' => $this->model,
            'created_at' => $this->createdAt,
            'finished_at' => $this->finishedAt,
            'fine_tuned_model' => $this->fineTunedModel,
            'hyperparameters' => $this->hyperparameters->toArray(),
            'organization_id' => $this->organizationId,
            'result_files' => $this->resultFiles,
            'status' => $this->status,
            'validation_file' => $this->validationFile,
            'training_file' => $this->trainingFile,
            'trained_tokens' => $this->trainedTokens,
            'error' => $this->error?->toArray(),
        ];
    }
}

<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\Customer;

class BatchPolicy
{
    /**
     * Determine whether the customer can view access logs.
     */
    public function viewAccessLogs(Customer $customer, Batch $batch): bool
    {
        return $customer->id === $batch->customer_id;
    }

    /**
     * Determine whether the customer can update the model.
     */
    public function update(Customer $customer, Batch $batch): bool
    {
        return $customer->id === $batch->customer_id;
    }

    /**
     * Determine whether the customer can delete the model.
     */
    public function delete(Customer $customer, Batch $batch): bool
    {
        return $customer->id === $batch->customer_id;
    }
}

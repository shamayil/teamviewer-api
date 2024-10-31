<?php

namespace App\Contracts;

interface Devices
{
    /**
     * Check values
     * @param array $columns
     * @return array
     */
    public function handle(array $values);

    /**
     * Check values
     * @param array $columns
     * @return array
     */

    public function edit(array $values);

    /**
     * Check values
     * @param array $columns
     * @return array
     */

    public function remove(array $values);

    /**
     * Check values
     * @param array $columns
     * @return array
     */

    public function device(array $values);

}

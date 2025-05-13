<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;

class DatabaseController extends Controller
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Migrate and seed the database.
     */
    public function database(): RedirectResponse
    {
        $response = $this->databaseManager->migrateAndSeed();

        return redirect()->route('LaravelInstaller::final')->with(['message' => $response]);
    }
}

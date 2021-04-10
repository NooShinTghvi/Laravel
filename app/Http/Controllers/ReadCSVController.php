<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ReadCSVController extends Controller
{
    public function readFile()
    {
        if (($h = fopen("cars.csv", "r")) !== FALSE) {
            // Convert each line into the local $data variable
            while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                // Read the data from a single line
            }

            // Close the file
            fclose($h);
        }
    }
}

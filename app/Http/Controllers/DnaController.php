<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Dna;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DnaController extends Controller
{

    public function isForceUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dna' => 'required',
        ]);

        $errors = array();
        if ($validator->fails()) {

            $e_index = 0;
            foreach($validator->errors()->messages() as $key=>$errorsmsges) {

                $errors[$e_index++] = $errorsmsges[0];
            }
        }

        if (count($errors) > 0) {

            return response()->json(['Error' => 'Bad query structure.'],403);

        } else {

            $force = 0;

            foreach ($request->dna as $dna) {
                \Log::info("". json_encode(count_chars($dna, 1)));

                foreach (count_chars($dna, 1) as $i => $val) {

                    if($val >= 4){

                        $cadena = ''.chr($i).chr($i).chr($i).chr($i);
                        $serch = strpos($dna, $cadena);
                        break;
                    } else {
                        $serch = NULL;
                    }
                }
                \Log::info("". $serch);

                if (isset($serch) && !is_null($serch)) {
                    if ($serch === false) {

                        Dna::create(
                            [
                                'dna_code' => $dna,
                                'type' => 'nonforce',
                                'created_at' => Carbon::now()->toDateTimeString(),
                            ]
                        );
                    } else {

                        Dna::create(
                            [
                                'dna_code' => $dna,
                                'type' => 'force',
                                'created_at' => Carbon::now()->toDateTimeString(),
                            ]
                        );
                        $force++;
                    }
                } else {
                    Dna::create(
                        [
                            'dna_code' => $dna,
                            'type' => 'nonforce',
                            'created_at' => Carbon::now()->toDateTimeString(),
                        ]
                    );
                }
            }
            if ($force > 0) {
                return response()->json(['Success' => 'Individual is force sensitive.'],200);
            } else {
                return response()->json(['Error' => 'Individual is Non Force-User.'],403);
            }
        }
    }

    public function statsDna(Request $request)
    {

        $force_user_dna = Dna::where([
            ['type', '=', 'force'],
        ])->count();

        $non_force_user_dna = Dna::where([
            ['type', '=', 'nonforce'],
        ])->count();

        $data = [
            "force_user_dna" => $force_user_dna,
            "non_force_user_dna" => $non_force_user_dna,
            "ratio" => $force_user_dna / $non_force_user_dna
        ];

        return response()->json($data,200);

    }
    public function resetDna(Request $request)
    {
        try {

            Dna::truncate();

            return response()->json(['Success' => 'Database Reset successfully.'],200);

        } catch (\Throwable $th) {

            return response()->json(['Error' => $th->getMessage()],403);

        }

    }

}

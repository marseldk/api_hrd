<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function logon(Request $request){
        $username = $request->username;
        $password = $request->password;
        $user = DB::connection('mysql')->select("select * from user where nama_user = '$username'");
        if (empty($user) || count($user) == 0) {
            return response()->json([
                "success" => false,
                "message" => "Invalid role. Please contact support.",
              ]); 
        }
        if ($user[0]->password == md5($password)) {
            return response()->json([
                "success" => true,
                "role" => $user[0]->role,
                "username" => $user[0]->nama_user,
              ]);
        }else{
            return response()->json([
                "success" => false,
                "message" => "Invalid username or password",
              ]); 
        }
    }

    public function absen(Request $request){
        $data = $request->all();
        $username = $request->username;
        $tanggal = $request->tanggal;
        $jam = $request->jam;
        // Ambil file gambar yang diunggah
        $image = $request->file('image');
        $jamname = $jam = str_replace(':', '', $jam);

        $countabsen = DB::connection('mysql')->select("select * from user_absen where username = '$username' and tanggal = '$tanggal'");

        if (count($countabsen) < 2) {
            // Buat nama unik untuk gambar
            $imageName = $tanggal.'-'.$jamname.'-'.$username.'.'. $image->getClientOriginalExtension();
            // $imageName = $tanggal.'-'.$jamname.'-'.$username;
             
            // Simpan gambar ke storage/public
            $imagePath = $image->storeAs('uploads', $imageName, 'public');  // Pastikan folder 'uploads' ada di storage/app/public

            $value=[
                'username'=> $username,
                'tanggal'=>$tanggal,
                'waktu' => $jam,
                'image_name' => $imageName
                ]; 

            $insertAbsen = DB::connection('mysql')->table('user_absen')->insert($value);
            
            return response()->json([
                "success" => true,
                "message" => 'Absen successfully submited!',
              ]);

        }else{
            return response()->json([
                "success" => false,
                "message" => 'Attendance cannot be more than 2 times!',
              ]);
        }
    }

    public function getKaryawan()
    {
        try {
            // Query data dari tabel users
            $users = DB::connection('mysql')->select("select * from user");
            
            // Cek apakah data ditemukan
            if (empty($users)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found',
                ], 404);
            }

            // Kembalikan respon JSON
            return response()->json([
                'success' => true,
                'message' => 'Karyawan data retrieved successfully',
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            // Handle error
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function addKaryawan(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'nama_user' => 'required|string|max:255',
                'role'      => 'required|integer|in:1,2', // 1 untuk User, 2 untuk HRD
                'gender'    => 'required|integer|in:1,2', // L untuk Laki-laki, P untuk Perempuan
                'alamat'    => 'required|string|max:500',
            ]);

            // Ambil username dari frontend (atau session jika ada autentikasi)
            $username = $request->input('username') ?? 'SYSTEM';

            // Cek apakah username sudah digunakan
            $existingUser = DB::connection('mysql')->table('user')->where('nama_user', $validatedData['nama_user'])->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username sudah terpakai.',
                ],);
            }

            // Data yang akan diinsert
            $value = [
                'nama_user'   => $validatedData['nama_user'],
                'role'        => $validatedData['role'],
                'gender'      => $validatedData['gender'],
                'alamat'      => $validatedData['alamat'],
                'password'    => md5('test123'),
                'created_date'=> now(), // Menggunakan Laravel helper
                'created_by'  => $username,
            ];

            // Insert ke tabel users
            $inserted = DB::connection('mysql')->table('user')->insertGetId($value);  // insertGetId untuk mendapatkan ID yang baru dimasukkan

            // Ambil data karyawan yang baru dimasukkan berdasarkan ID
            $karyawan = DB::connection('mysql')->table('user')->where('id_user', $inserted)->first();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan added successfully',
                'data'    => $karyawan, // Mengembalikan data karyawan yang baru ditambahkan
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan karyawan.',
                'error'   => $e->getMessage(),
            ], );
        }
    }

    public function deleteKaryawan($id)
    {
        $karyawan = DB::connection('mysql')->table('user')->where('id_user', $id)->delete();

        if ($karyawan) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan deleted successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan not found',
            ], 404);
        }
    }

    public function updateKaryawan(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|integer|exists:user,id_user',
            'nama_user' => 'required|string',
            'gender' => 'nullable|integer',
            'alamat' => 'nullable|string',
            'role' => 'required|integer|in:1,2', // 1 = User, 2 = HRD
        ]);

        // Menemukan data karyawan berdasarkan id_user
        $karyawan = DB::connection('mysql')->table('user')->where('id_user', $request->id_user)->first();

        if (empty($karyawan)) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan not found.',
            ],);
        }

        // Ambil username dari frontend (atau session jika ada autentikasi)
        $username = $request->input('username') ?? 'SYSTEM';

        // Update data karyawan
        $updated = DB::connection('mysql')->table('user')
            ->where('id_user', $request->id_user)
            ->update([
                'nama_user' => $request->nama_user,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
                'role' => $request->role,
                'updated_date' => now(),
                'updated_by'  => $username,
            ]);

        if ($updated) {
            $updatedKaryawan = DB::connection('mysql')->table('user')->where('id_user', $request->id_user)->first();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan updated successfully',
                'data' => $updatedKaryawan,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update karyawan',
            ]);
        }
    }

    public function getData()
    {
        $data = DB::table('user_absen') 
            ->select('id_absen', 'username', 'tanggal', 'waktu', 'image_name')
            ->get();

        // Cek jika data ditemukan
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'data' => $data,
        ]);
    }



}

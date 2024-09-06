<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
class MemberController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'address' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:13'
        ]);

        // Create member
        $member = Member::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'phoneNumber' => $request->input('phoneNumber'),
        ]);

        // Return a response
        return response()->json([
            'message' => 'Member added successfully!',
            'member' => $member,
        ], 201);
    }

    public function index(Request $request)
{
    // Retrieve all members
    $members = Member::all();

    return response()->json([
        'data' => $members,
    ]);
}
}

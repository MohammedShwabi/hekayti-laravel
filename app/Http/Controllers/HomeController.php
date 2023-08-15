<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Story;
use App\Models\Admin;
use Carbon\Carbon;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        return view('home');
    }

   

    // get the number of users registered in each month to use in dashboard
    public function getUserGrowthData()
    {
        $userGrowth = User::select('created_at')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($user) {
                // return $user->created_at->format('Y-m');
                return Carbon::parse($user->created_at)->isoFormat('MMM'); // Use isoFormat() with 'MMM' for abbreviated month name
            })
            ->map(function ($group) {
                return count($group);
            });

        return $userGrowth;
    }

    // get number of stories in each level to use in dashboard
    public function storyLevelCount()
    {
        $levelCounts = Story::select('level')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level');

        return $levelCounts;
    }

    // get the count of admins to use in dashboard
    public function adminCount()
    {
        $adminCount = Admin::where('role', 'manager')->count();
        return $adminCount;
    }

    // get the user count and group them using the character filed
    public function userCount()
    {
        $userCount = User::select('character')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('character')
            ->pluck('count', 'character');

        return $userCount;
    }

    // get the number of users in each level to use in dashboard
    public function userLevelCount()
    {
        $userLevelCount = User::select('level')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level');

        return $userLevelCount;
    }


    // to pass the data to dashboard page
    public function showChart()
    {
        $userGrowth = $this->getUserGrowthData();
        $levelCounts = $this->storyLevelCount();
        $adminCount = $this->adminCount();
        $userCount = $this->userCount();
        $userLevelCount = $this->userLevelCount();

        return view('home', compact('userGrowth', 'levelCounts', 'adminCount', 'userCount', 'userLevelCount'));
    }
}

<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\User\Report;
use Illuminate\Http\Request;

class OwnerReportController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Get All Reports :
    public function getAllReports()
    {
        $reports = Report::with(['user', 'guide'])->get();
        return response()->json([
            'reports' => $reports,
            'reports_count' => $reports->count(),
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Get Report :
    public function getReport($id)
    {
        $report = Report::with(['user', 'guide'])->find($id);

        if (!$report) {
            return Response::Message("Report not found", 404);
        }

        return response()->json(['report' => $report], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Update Report Status :
    public function updateReportStatus(Request $request, $id)
    {
        $report = Report::find($id);

        if (!$report) {
            return Response::Message("Report not found", 404);
        }

        $report->status = $request->input('status');
        $report->save();

        return Response::Message("Report status updated successfully", 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Report :
    public function deleteReport($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return Response::Message("Report not found", 404);
        }

        $report->delete();
        return Response::Message("Report deleted successfully", 200);
    }
}


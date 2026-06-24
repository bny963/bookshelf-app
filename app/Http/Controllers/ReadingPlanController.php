<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\ReadingPlanStoreRequest;
use App\Http\Requests\ReadingPlanUpdateRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    public function index(Request $request): View
    {
        $currentStatus = $request->query('status');

        $query = Auth::user()->readingPlans()->with('book')->latest();

        if ($currentStatus && ReadingPlanStatus::tryFrom($currentStatus)) {
            $query->where('status', $currentStatus);
        }

        $readingPlans = $query->get();

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    public function create(): View
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    public function store(ReadingPlanStoreRequest $request): RedirectResponse
    {
        Auth::user()->readingPlans()->create(array_merge($request->validated(), [
            'status' => ReadingPlanStatus::Planning,
        ]));

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を登録しました。');
    }

    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);

        return view('reading-plans.edit', compact('readingPlan'));
    }

    public function update(ReadingPlanUpdateRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->update($request->validated());

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);

        $readingPlan->delete();

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }

    public function complete(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('complete', $readingPlan);

        $readingPlan->update([
            'status'       => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読了しました！');
    }
}

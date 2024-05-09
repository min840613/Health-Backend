<?php

namespace App\Http\Controllers\App;

use App\Enums\NotificationsStatus;
use App\Enums\NotificationsType;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsStore;
use App\Http\Requests\NotificationsUpdate;
use App\Models\App\NotificationsModel;
use App\Services\NotificationsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotificationsController
 * @package App\Http\Controllers\App
 */
class NotificationsController extends Controller
{
    /** @var string */
    private $roleName = 'notifications';

    /** @var string */
    private $siteName = 'notifications';

    /** @var NotificationsService */
    private NotificationsService $service;

    public function __construct(NotificationsService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data = request()->all();

        $field = ['創建者', '狀態', '推播類別', '推播標題', '推播對象', '創建時間', '推播時間'];

        $collections = $this->service->find($data);

        $filters = [
            'push_notifications_status' => $this->service::PUSH_STATUS_OPTIONS,
            'type' => $this->service::TYPE_OPTIONS,
        ];

        return view('app.notifications.index', [
            'role_name' => $this->roleName,
            'field' => $field,
            'datas' => $collections,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $data = $this->service->getBasicData($request);

        return view('app.notifications.create', [
            'role_name' => $this->roleName,
            'site_name' => $this->siteName,
            'is_need_detail' => $request->input('article_id') !== null || $request->input('shake_id') !== null,
            'is_article' => $request->input('article_id') !== null,
            'is_shake' => $request->input('shake_id') !== null,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NotificationsStore $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(NotificationsStore $request): RedirectResponse
    {
        $data = $request->validated();

        $this->service->store($data, \Auth::user());

        return redirect()->route('notifications.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     * @throws \Exception
     */
    public function edit($id): View
    {
        $model = NotificationsModel::find($id);

        if ($model === null) {
            throw new \Exception('model notfound.');
        }

        $this->service->appendRelationColumn($model);

        return view('app.notifications.edit', [
            'role_name' => $this->roleName,
            'site_name' => $this->siteName,
            'notification' => $model,
            'is_need_detail' => in_array($model->type, [NotificationsType::ARTICLE, NotificationsType::SHAKE]),
            'is_article' => $model->type === NotificationsType::ARTICLE,
            'is_shake' => $model->type === NotificationsType::SHAKE,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @param NotificationsUpdate $request
     * @return Response
     * @throws \Throwable
     */
    public function update(string $id, NotificationsUpdate $request): Response
    {
        $notification = NotificationsModel::find($id);

        if ($notification === null) {
            throw new \Exception('model notfound.');
        }

        $data = $request->validated();

        $this->service->update($notification, $data, \Auth::user());

        return redirect()->route('notifications.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function destroy($id): Response
    {
        $notification = NotificationsModel::find($id);

        if ($notification === null) {
            throw new \Exception('model notfound.');
        }

        $notification->push_notifications_status = NotificationsStatus::CANCEL;
        $notification->save();

        return response()->success([]);
    }

    /**
     * @param int $articleId
     * @return Response
     */
    public function validateArticleId(int $articleId): Response
    {
        $isRepeat = $this->service->validateRepeatArticle($articleId);

        return response()->success(['is_repeat' => $isRepeat]);
    }
}

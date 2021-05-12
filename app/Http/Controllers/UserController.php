<?php


    namespace App\Http\Controllers;

    use App\Entities\User;
    use App\Http\Requests\Course\CreateRequest;
    use App\Repositories\Contracts\PermissionRepositoryInterface;
    use App\Repositories\Contracts\RoleRepositoryInterface;
    use App\Entities\Role;
    use App\Repositories\Contracts\UserRepositoryInterface;
    use App\Repositories\EloquentUserRepository;
    use App\Traits\Authorizable;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;


    class UserController extends Controller
    {
        use Authorizable;
        /**
         * @var UserRepositoryInterface
         */
        private $userRepository;
        /**
         * @var PermissionRepositoryInterface
         */
        private $permissionRepository;

        /**
         * RoleController constructor.
         * @param RoleRepositoryInterface $roleRepository
         * @param PermissionRepositoryInterface $permissionRepository
         */
        public function __construct(UserRepositoryInterface $userRepository, PermissionRepositoryInterface $permissionRepository)
        {
            $this->userRepository = $userRepository;
            $this->permissionRepository = $permissionRepository;
        }


        /**
         * Display a listing of the resource.
         *
         */
        public function index()
        {
            $users = $this->userRepository->all();

            return view('users.index', compact('users'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @param int $course
         */
        public function create()
        {
            $user = '';
            $roles = Role::all();
            return view('users.create', compact('roles'));
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param Request                 $request
         * @return RedirectResponse
         */
        public function store(Request $request)
        {
//            $this->validate($request, ['name' => 'required|unique:roles']);
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            if ($user->save()){
                return redirect()->route('users.index');
            } else {

                return redirect()->route('users.index');
            }
        }

        /**
         * Display the specified resource.
         *
         * @param int $id
         * @param ShowLessonUsecaseInterface $lessonUsecase
         * @return \Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\View\View
         */
        public function show($id, ShowLessonUsecaseInterface $lessonUsecase)
        {
            $response = $lessonUsecase->handle($id, Auth::user()->id);
            $lesson = $response['data']['lesson'];
            $subscribed = $response['data']['subscribed'];
            if(!$subscribed){
                return redirect()->route("courses.show", $lesson->course->id);
            }

            return view('lessons.show', compact('lesson', 'subscribed'));
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param int                     $id
         * @param UserRepositoryInterface $userRepository
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function edit(int $id, UserRepositoryInterface $userRepository)
        {
            $ar['user'] = $userRepository->findById($id);
            $ar['roles'] = Role::all();

            return view('users.edit', $ar);
        }

        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param int $id
         * @param UpdateLessonUsescaseInterface $updateLessonUsescase
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id, UpdateLessonUsescaseInterface $updateLessonUsescase)
        {
            try {
                $updateLessonUsescase->handle($id, $request->all());
                flash('Урок успешно сохранен');
            } catch (\Exception $e){
                flash('Урок не удалось сохранить', 'error');
            }
            return  redirect()->back();
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @param DeleteLessonUsescaseInterface $deleteLessonUsescase
         * @return \Illuminate\Http\Response
         */
        public function destroy($id, DeleteLessonUsescaseInterface $deleteLessonUsescase)
        {
            try {
                $deleteLessonUsescase->handle($id);
                flash('Урок успешно удален');
            } catch (\Exception $e){
                flash('Урок не может быть удален', 'error');
            }
            return redirect()->back();
        }

    }

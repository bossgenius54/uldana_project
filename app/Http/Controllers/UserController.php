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
         * @param UserRepositoryInterface $updateLessonUsescase
         * @return RedirectResponse
         */
        public function update(Request $request, $id, UserRepositoryInterface $userRepository)
        {
            $user = $userRepository->findById($id);
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;

            foreach ($user->getRoleNames() as $role){
                $user->removeRole($role);
            }
            $user->assignRole($request->role);

            if ($user->save()){
                flash('Пользователь успешно добавлен');
            } else {
                flash('Не удалось изменить!');
                return redirect()->back();
            }

            return  redirect()->route('users.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param int                     $id
         * @param UserRepositoryInterface $userRepository
         * @return RedirectResponse
         */
        public function destroy(int $id, UserRepositoryInterface $userRepository)
        {
            $user = $userRepository->findById($id);
            $name = $user->name;

            if ($user->delete()){
                flash("Пользователь под именем $name успешно удален");
            } else {
                flash("Пользователь под именем $name не удалось удалить");
            }

            return redirect()->back();
        }

    }

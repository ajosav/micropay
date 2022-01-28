<?php

namespace App\Repository;

use ErrorException;
use App\Models\User;
use App\Enums\StatusCode;
use Illuminate\Support\Facades\DB;
use App\Exceptions\MicropayException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use App\Contracts\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(array $user_record) 
    {
        try {
            $user = DB::transaction(function () use($user_record) {
                $user = $this->user->firstOrCreate($user_record);
                event(new Registered($user));
                return $user;
            });
            return $user;
        } catch(QueryException $e){
            report($e);
            throw new MicropayException("User registration failed! Please try again later", StatusCode::BAD_RESPONSE);
        } catch (ErrorException $e) {
            report($e);
            throw new MicropayException("User registration failed! Please try again later", StatusCode::BAD_RESPONSE);
        }
    }

    public function all(? int $per_page = 50) 
    {
        return $this->user->paginate($per_page);
    }

    public function findUser($id)
    {
        return $this->user->findOrFail($id);
    }

    public function findMany($ids)
    {
        return $this->user->findOrFail($ids);
    }

    public function update($id, array $records)
    {
        $user = $this->user->findOrFail($id);
        
        return tap($user)->update($records);
        
    }

    public function delete($id){
        $user = $this->user->findOrFail($id);
        return $user->delete();
    }

}
<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ExportableTrait as Exportable;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Traits\ObfuscatableTrait as ObfuscatesID;
use App\Traits\CamelCaseAttributesTrait as CamelCaseAttrs;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use Authenticatable, CamelCaseAttrs, CanResetPassword, Exportable, HasRoles, ObfuscatesID, SoftDeletes;


    //
    //
    // Attributes used by App\Traits\ExportableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * The attributes that should be hidden from the model's array form when exporting data to file.
     */
    protected $hiddenFromExport = [
        'id',
    ];

    //
    //
    // Attributes for App\Traits\ObfuscatableTrait
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * @var int
     */
    public $obfuscatorId = 89;


    //
    //
    // Main attributes
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


    //
    //
    // Accessors and mutators.
    //
    ////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Accessor for $this->uri.
     *
     * @return string
     */
    public function getUriAttribute() {
        return route('user.show', $this->uniqueId);
    }

    /**
     * Accessor for $this->editUri.
     *
     * @return string
     */
    public function getEditUriAttribute() {
        return route('r.user.edit', ['id' => $this->uniqueId, 'return' => 'summary']);
    }

    /**
     * Accessor for $this->editUriAdmin.
     *
     * @return string
     */
    public function getEditUriAdminAttribute() {
        return route('r.user.edit', ['id' => $this->uniqueId, 'return' => 'admin']);
    }
}

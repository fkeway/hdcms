<?php

namespace App;

use App\Models\Attachment;
use App\Models\Group;
use App\Models\Pay;
use App\Models\Site;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use App\Traits\Site as SiteTrait;

class User extends Authenticatable
{
    use SiteTrait, Notifiable, HasRoles, HasApiTokens, LogsActivity;
    protected static $recordEvents = ['created'];
    protected static $logName = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'mobile',
        'email_verified_at',
        'token',
        'mobile_verified_at',
        'icon',
        'home',
        'weibo',
        'wechat',
        'github',
        'qq',
        'admin_end',
        'lock',
        'group_id',
        'favour_count',
        'real_name',
    ];
    protected $casts = ['lock' => 'boolean', 'admin_end' => 'datetime'];

    /**
     * 动态自定义字段赋值
     * @param Activity $activity
     * @param string $eventName
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->site_id = \site()['id'];
        $activity->module_id = \module()['id'];
        $activity->module = \module()['name'];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 按用户名查找
     * @param $query
     * @param string $name
     * @return mixed
     */
    public function scopeName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * 返回当前模型的链接
     * @return string
     */
    public function getLink()
    {
        return route('user.home', $this);
    }

    /**
     * 返回当前模型的标题
     * @return string
     */
    public function getTitle()
    {
        return $this['name'];
    }

    public function favourUpdate()
    {
        $this['favour_count'] = $this->favourCount();
        return $this->save();
    }

    /**
     * 我的粉丝
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fans()
    {
        return $this
            ->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->wherePivot('site_id', \site()['id'])->withTimestamps();
    }

    /**
     * 支付定单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pays()
    {
        return $this->hasMany(Pay::class, 'user_id');
    }

    /**
     * 我关注册的人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')->wherePivot('site_id',
            \site()['id'])->withTimestamps();
    }

    /**
     * 指定用户是否为我的粉丝
     * @param User $user
     * @return mixed
     */
    public function hasFans(User $user)
    {
        return $this->fans->contains($user);
    }

    /**
     * 我是否关注指定用户
     * @param User $user
     * @return mixed
     */
    public function following(User $user)
    {
        return $this->followers->contains($user);
    }

    /**
     * 接口用户条件
     * @param $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        \site(null, true);
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $where['email'] = $username :
            $where['mobile'] = $username;
        $user = self::where($where)->first();
        if ($user && $user->sites->contains(\site())) {
            return $user;
        }
    }

    public function getAvatarAttribute()
    {
        return $this['icon'] ? url($this['icon']) : asset('images/system/user.jpg');
    }

    /**
     * 会员组关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * 附件关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachment()
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * 超级管理员
     * @return bool
     */
    public function is_super_admin()
    {
        return $this['id'] == 1;
    }

    /**
     * 用户站点关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sites()
    {
        return $this->belongsToMany(Site::class)
            ->withPivot('role')->as('role')->withTimestamps();
    }
}

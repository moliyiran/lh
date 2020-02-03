<?php


use think\facade\Route;
// 注册路由到News控制器的read操作
Route::rule('login','/nologin/login');
Route::rule('admin','/nologin/admin');
Route::rule('subnews','/index/subNews');
Route::rule('mylist','/index/myList');
Route::rule('delnews','/index/delete');
Route::rule('dellogin','/index/dellogin');
Route::rule('backlist','/index/backlist');



/*Route::rule('kaijvideo','/index/kaijvideo');
Route::rule('sixcolor_animate','/animate/sixColor');
Route::rule('yctk','/index/yctk');
Route::rule('tbtj_:name','/index/tbtj');
Route::rule('fastsix','/index/fastSix');
Route::rule('fast_:name','/index/fastTbtj');
Route::rule('assist','/index/countAsist');
Route::rule('sixvote','/index/sixvote');
Route::rule('news:type','/index/news');
Route::rule('detail:id','/index/detail');
Route::rule('findNewsByPIdForPage.do','/index/newList');
Route::rule('category:id','/index/category');
Route::rule('findNewsParticularById.do','/index/newContent');*/












<?php

Route::post('comments/{comment}/complaints', config('comments.controller') . '@complain')->name('comments.complain');
Route::delete('comments/{comment}', config('comments.controller') . '@destroy')->name('comments.destroy');
Route::get('{target}/{id}/comments', config('comments.controller') . '@index')->name('comments.index');
Route::post('{target}/{id}/comments', config('comments.controller') . '@store')->name('comments.store');

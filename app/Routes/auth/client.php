<?php

Route::resource('clients', 'Web\ClientsController', ['only' => ['index','create','edit']]);
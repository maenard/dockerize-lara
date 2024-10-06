<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

$controllers_path = app_path('Http/Controllers');
$controller_files = File::allFiles($controllers_path);

foreach ($controller_files as $controller_file) {
    $prefix = $controller_file->getRelativePath();
    $not_included_relative_path = ["Auth"];

    if(!in_array($prefix, $not_included_relative_path)) {
        $path_name = $controller_file->getPathname();
        $relative_class_path = str_replace(
            ['/', '.php'],
            ['\\', ''],
            $controller_file->getRelativePathname()
        );

        $controller_class = 'App\\Http\\Controllers\\' . $relative_class_path;

        if(class_exists($controller_class)) {
            $reflector = new ReflectionClass($controller_class);

            if($reflector->isSubclassOf(Controller::class)) {
                $class_methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

                foreach ($class_methods as $key => $method) {
                    if($method->class === $controller_class && !$method->isConstructor()){
                        $doc_comment = $method->getDocComment();
                        $http_methods = [];
                        $middlewares = [];

                        if ($doc_comment) {
                            if (preg_match('/@http\s+([a-zA-Z|]+)/', $doc_comment, $matches)) {
                                $http_methods = explode('|', $matches[1]);
                                $http_methods = array_map('trim', $http_methods);
                            }

                            if (preg_match('/@middleware\s+([a-zA-Z0-9:.|]+)/', $doc_comment, $matches)) {
                                $middlewares = explode('|', $matches[1]);
                                $middlewares = array_map('trim', $middlewares);
                            }
                        }else{
                            $http_methods = ['POST'];
                        }

                        $method_name = $method->getName();
                        $route = Str::kebab(str_replace('Controller', '', class_basename($controller_class)));
                        $parameters = $method->getParameters();

                        $prefix = strtolower($prefix);

                        $method_name = Str::kebab(str_replace('_', '-', $method_name));
                        $method_name = $method_name == 'index' ? '' : "/$method_name";
                        $end_point = ($prefix ? "$prefix/": '') . "$route$method_name";

                        foreach($parameters as $parameter){
                            $parameter_name = $parameter->getName();
                            $type = $parameter->getType();
                            $param_class = $type ? $type->getName() : '';

                            if(!$param_class == "Illuminate\Http\Request" && !is_subclass_of($param_class, FormRequest::class)) {
                                $end_point .= "/{" . "$parameter_name}";
                            }

                        }

                        Route::match($http_methods, $end_point, [$controller_class, $method_name])->middleware($middlewares);
                    }
                }
            }
        }
    }
}

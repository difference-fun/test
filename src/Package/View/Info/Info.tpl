{{R3M}}
{{$request = request()}}
Package: {{$request.package}}

Module: {{$request.module|uppercase.first}}

{{if(!is.empty($request.submodule))}}
Submodule: {{$request.submodule|uppercase.first}}
{{/if}}
{{if($request.module === 'info')}}
{{$files = dir.read(config('controller.dir.view') + 'Object/')}}
{{$files = data.sort($files, ['url' => 'ASC'])}}
Commands:
{{for.each($files as $file)}}
{{$file.basename = file.basename($file.name, config('extension.tpl'))}}
{{binary()}} {{$request.package}} object {{$file.basename|lowercase}}

{{/for.each}}
{{else}}
{{$options = options()}}
{{$is.all = false}}
{{if(is.empty.object($options))}}
{{$is.all = true}}
{{$files = dir.read(config('controller.dir.view') + 'Object/Info/')}}
{{$files = data.sort($files, ['url' => 'ASC'])}}
Options:
{{for.each($files as $file)}}
{{if($file.name === 'Object.Info.tpl')}}
{{continue()}}
{{/if}}
{{$file.basename = file.basename($file.name, config('extension.tpl'))}}
{{if(!is.empty($options[$file.basename|lowercase]) || !is.empty($is.all))}}
{{binary()}} {{$request.package}} {{$request.module}} {{$request.submodule}} -{{$file.basename|lowercase}}

{{/if}}
{{/for.each}}
{{else}}
{{$files = dir.read(config('controller.dir.view') + 'Object/Info/')}}
{{$files = data.sort($files, ['url' => 'ASC'])}}
{{for.each($files as $file)}}
{{if($file.name === 'Object.Info.tpl')}}
{{continue()}}
{{/if}}
{{$file.basename = file.basename($file.name, config('extension.tpl'))}}
{{if(!is.empty($options[$file.basename|lowercase]) || !is.empty($is.all))}}
{{require($file.url)}}
{{/if}}
{{/for.each}}
{{/if}}
{{/if}}
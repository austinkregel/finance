@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <add-ticker></add-ticker>
                <list-ticker></list-ticker>
            </div>
        </div>
    </div>
</home>
@endsection

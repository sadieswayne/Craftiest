@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.environment.wizard.templateTitle') }}
@endsection

@section('title')
    <i
        class="fa fa-magic fa-fw"
        aria-hidden="true"
    ></i>
    {!! trans('installer_messages.environment.wizard.title') !!}
@endsection

@section('container')
    <h4 class="mb-5 mt-0 font-body text-[26px]">{{ __('Setup') }}</h4>

    <div class="tabs tabs-full">

        <input
            class="peer/tab1 invisible absolute h-0 w-0"
            class="tab-input"
            id="tab1"
            type="radio"
            name="tabs"
            checked
        />
        <label
            class="mx-3 inline-flex cursor-pointer rounded-md px-3 py-1 font-medium transition-colors peer-checked/tab1:bg-black peer-checked/tab1:bg-opacity-5"
            for="tab1"
        >
            {{ trans('installer_messages.environment.wizard.tabs.environment') }}
        </label>

        <input
            class="peer/tab2 invisible absolute h-0 w-0"
            class="tab-input"
            id="tab2"
            type="radio"
            name="tabs"
        />
        <label
            class="mx-3 inline-flex cursor-pointer rounded-md px-3 py-1 font-medium transition-colors peer-checked/tab2:bg-black peer-checked/tab2:bg-opacity-5"
            for="tab2"
        >
            {{ trans('installer_messages.environment.wizard.tabs.database') }}
        </label>

        <hr class="-mx-10 mb-10 mt-6 w-auto opacity-50">

        <form
            class="text-start text-[15px] peer-checked/tab1:[&_#tab1content]:block peer-checked/tab2:[&_#tab2content]:block peer-checked/tab3:[&_#tab3content]:block [&_input:not([type=radio])]:block [&_input:not([type=radio])]:h-10 [&_input:not([type=radio])]:w-full [&_input:not([type=radio])]:rounded-xl [&_input:not([type=radio])]:border [&_input:not([type=radio])]:border-solid [&_input:not([type=radio])]:bg-transparent [&_input:not([type=radio])]:px-4 [&_label]:mb-1 [&_label]:mt-6 [&_label]:block [&_label]:text-[14px] [&_label]:font-medium [&_label]:opacity-70 [&_select]:block [&_select]:h-10 [&_select]:w-full [&_select]:rounded-xl [&_select]:border [&_select]:border-solid [&_select]:bg-transparent [&_select]:px-4"
            method="post"
            action="{{ route('LaravelInstaller::environmentSaveWizard') }}"
        >
            <div
                class="hidden"
                id="tab1content"
            >
                <input
                    type="hidden"
                    name="_token"
                    value="{{ csrf_token() }}"
                >

                <div class="form-group {{ $errors->has('app_name') ? ' has-error ' : '' }}">
                    <label for="app_name">
                        {{ trans('installer_messages.environment.wizard.form.app_name_label') }}
                    </label>
                    <input
                        id="app_name"
                        type="text"
                        name="app_name"
                        value=""
                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_name_placeholder') }}"
                    />
                    @if ($errors->has('app_name'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('app_name') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('app_url') ? ' has-error ' : '' }}">
                    <label for="app_url">
                        {{ trans('installer_messages.environment.wizard.form.app_url_label') }}
                    </label>

                    <input
                        id="app_url"
                        type="url"
                        name="app_url"
                        value="{{ url('/') }}"
                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_url_placeholder') }}"
                    />
                    @if ($errors->has('app_url'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('app_url') }}
                        </span>
                    @endif
                    <x-alert class="mt-2">
                        <p>
                            {{ __('Please do not enter / at the end of the url. For Example; https://liquid-themes.com') }}
                        </p>
                    </x-alert>
                </div>

                <details class="mb-32 mt-9 open:mb-4">
                    <summary class="flex cursor-pointer list-none items-center gap-4 text-[15px]">
                        <span class="inline-flex grow items-center">
                            <span class="inline-block h-px w-full bg-black bg-opacity-5"></span>
                        </span>
                        <span class="inline-flex items-center gap-1 font-medium">
                            Advanced Options
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M6 9l6 6l6 -6"></path>
                            </svg>
                        </span>
                        <span class="inline-flex grow items-center">
                            <span class="inline-block h-px w-full bg-black bg-opacity-5"></span>
                        </span>
                    </summary>
                    <div class="form-group {{ $errors->has('environment') ? ' has-error ' : '' }}">
                        <label for="environment">
                            {{ trans('installer_messages.environment.wizard.form.app_environment_label') }}
                        </label>
                        <select
                            id="environment"
                            name="environment"
                            onchange='checkEnvironment(this.value);'
                        >
                            <option
                                value="production"
                                selected
                            >{{ trans('installer_messages.environment.wizard.form.app_environment_label_production') }}</option>
                            <option value="local">{{ trans('installer_messages.environment.wizard.form.app_environment_label_local') }}</option>
                            <option value="development">{{ trans('installer_messages.environment.wizard.form.app_environment_label_developement') }}</option>
                            <option value="qa">{{ trans('installer_messages.environment.wizard.form.app_environment_label_qa') }}</option>
                            <option value="other">{{ trans('installer_messages.environment.wizard.form.app_environment_label_other') }}</option>
                        </select>
                        <div
                            class="mt-2"
                            id="environment_text_input"
                            style="display: none;"
                        >
                            <input
                                id="environment_custom"
                                type="text"
                                name="environment_custom"
                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_environment_placeholder_other') }}"
                            />
                        </div>
                        @if ($errors->has('environment'))
                            <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                                <i
                                    class="fa fa-fw fa-exclamation-triangle"
                                    aria-hidden="true"
                                ></i>
                                {{ $errors->first('environment') }}
                            </span>
                        @endif
                    </div>

                    <div class="form-group {{ $errors->has('app_debug') ? ' has-error ' : '' }}">
                        <label
                            class="!mb-2"
                            for="app_debug"
                        >
                            {{ trans('installer_messages.environment.wizard.form.app_debug_label') }}
                        </label>
                        <div class="flex items-center gap-3">
                            <label
                                class="!mt-0"
                                for="app_debug_true"
                            >
                                <input
                                    id="app_debug_true"
                                    type="radio"
                                    name="app_debug"
                                    value=true
                                />
                                {{ trans('installer_messages.environment.wizard.form.app_debug_label_true') }}
                            </label>
                            <label
                                class="!mt-0"
                                for="app_debug_false"
                            >
                                <input
                                    id="app_debug_false"
                                    type="radio"
                                    name="app_debug"
                                    value=false
                                    checked
                                />
                                {{ trans('installer_messages.environment.wizard.form.app_debug_label_false') }}
                            </label>
                        </div>
                        @if ($errors->has('app_debug'))
                            <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                                <i
                                    class="fa fa-fw fa-exclamation-triangle"
                                    aria-hidden="true"
                                ></i>
                                {{ $errors->first('app_debug') }}
                            </span>
                        @endif
                    </div>

                    <div class="form-group {{ $errors->has('app_log_level') ? ' has-error ' : '' }}">
                        <label for="app_log_level">
                            {{ trans('installer_messages.environment.wizard.form.app_log_level_label') }}
                        </label>
                        <select
                            id="app_log_level"
                            name="app_log_level"
                        >
                            <option
                                value="debug"
                                selected
                            >{{ trans('installer_messages.environment.wizard.form.app_log_level_label_debug') }}</option>
                            <option value="info">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_info') }}</option>
                            <option value="notice">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_notice') }}</option>
                            <option value="warning">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_warning') }}</option>
                            <option value="error">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_error') }}</option>
                            <option value="critical">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_critical') }}</option>
                            <option value="alert">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_alert') }}</option>
                            <option value="emergency">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_emergency') }}</option>
                        </select>
                        @if ($errors->has('app_log_level'))
                            <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                                <i
                                    class="fa fa-fw fa-exclamation-triangle"
                                    aria-hidden="true"
                                ></i>
                                {{ $errors->first('app_log_level') }}
                            </span>
                        @endif
                    </div>
                </details>

                <div class="mt-6 text-center">
                    <button
                        class="flex w-full items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white"
                        onclick="return showDatabaseSettings();"
                        type="button"
                    >
                        {{ trans('installer_messages.environment.wizard.form.buttons.setup_database') }}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div
                class="hidden"
                id="tab2content"
            >

                <div class="form-group {{ $errors->has('database_connection') ? ' has-error ' : '' }}">
                    <label for="database_connection">
                        {{ trans('installer_messages.environment.wizard.form.db_connection_label') }}
                    </label>
                    <select
                        id="database_connection"
                        name="database_connection"
                    >
                        <option
                            value="mysql"
                            selected
                        >{{ trans('installer_messages.environment.wizard.form.db_connection_label_mysql') }}</option>
                        <option value="sqlite">{{ trans('installer_messages.environment.wizard.form.db_connection_label_sqlite') }}</option>
                        <option value="pgsql">{{ trans('installer_messages.environment.wizard.form.db_connection_label_pgsql') }}</option>
                        <option value="sqlsrv">{{ trans('installer_messages.environment.wizard.form.db_connection_label_sqlsrv') }}</option>
                    </select>
                    @if ($errors->has('database_connection'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('database_connection') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('database_hostname') ? ' has-error ' : '' }}">
                    <label for="database_hostname">
                        {{ trans('installer_messages.environment.wizard.form.db_host_label') }}
                    </label>
                    <input
                        id="database_hostname"
                        type="text"
                        name="database_hostname"
                        value="127.0.0.1"
                        placeholder="{{ trans('installer_messages.environment.wizard.form.db_host_placeholder') }}"
                    />
                    @if ($errors->has('database_hostname'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('database_hostname') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('database_port') ? ' has-error ' : '' }}">
                    <label for="database_port">
                        {{ trans('installer_messages.environment.wizard.form.db_port_label') }}
                    </label>
                    <input
                        id="database_port"
                        type="number"
                        name="database_port"
                        value="3306"
                        placeholder="{{ trans('installer_messages.environment.wizard.form.db_port_placeholder') }}"
                    />
                    @if ($errors->has('database_port'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('database_port') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
                    <label for="database_name">
                        {{ trans('installer_messages.environment.wizard.form.db_name_label') }}
                    </label>
                    <input
                        id="database_name"
                        type="text"
                        name="database_name"
                        value=""
                        placeholder="{{ trans('installer_messages.environment.wizard.form.db_name_placeholder') }}"
                    />
                    @if ($errors->has('database_name'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('database_name') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('database_username') ? ' has-error ' : '' }}">
                    <label for="database_username">
                        {{ trans('installer_messages.environment.wizard.form.db_username_label') }}
                    </label>
                    <input
                        id="database_username"
                        type="text"
                        name="database_username"
                        value=""
                        placeholder="{{ trans('installer_messages.environment.wizard.form.db_username_placeholder') }}"
                    />
                    @if ($errors->has('database_username'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('database_username') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('database_password') ? ' has-error ' : '' }}">
                    <label for="database_password">
                        {{ trans('installer_messages.environment.wizard.form.db_password_label') }}
                    </label>
                    <input
                        id="database_password"
                        type="password"
                        name="database_password"
                        value=""
                        placeholder="{{ trans('installer_messages.environment.wizard.form.db_password_placeholder') }}"
                    />
                    @if ($errors->has('database_password'))
                        <span class="mt-1 block rounded-md bg-red-100 px-2 py-1 text-sm text-red-600">
                            <i
                                class="fa fa-fw fa-exclamation-triangle"
                                aria-hidden="true"
                            ></i>
                            {{ $errors->first('database_password') }}
                        </span>
                    @endif
                </div>

                <div class="mt-6 text-center">
                    <button
                        class="flex w-full items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white"
                        type="submit"
                    >
                        {{ trans('installer_messages.environment.wizard.form.buttons.install') }}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </form>

    </div>

    <script type="text/javascript">
        function checkEnvironment(val) {
            var element = document.getElementById('environment_text_input');
            if (val == 'other') {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }

        function showDatabaseSettings() {
            document.getElementById('tab2').checked = true;
        }

        function showApplicationSettings() {
            document.getElementById('tab3').checked = true;
        }
    </script>
@endsection

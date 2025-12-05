<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth_register.title')" :description="__('auth_register.description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('auth_register.name_label')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('auth_register.name_placeholder')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('auth_register.email_label')"
            type="email"
            required
            autocomplete="email"
            :placeholder="__('auth_register.email_placeholder')"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('auth_register.password_label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('auth_register.password_placeholder')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('auth_register.confirm_password_label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('auth_register.confirm_password_placeholder')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('auth_register.create_account_btn') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('auth_register.already_have_account') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('auth_register.login_link') }}</flux:link>
    </div>
</div>

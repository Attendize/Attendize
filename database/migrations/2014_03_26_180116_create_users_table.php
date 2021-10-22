<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statuses', function ($t) {
            $t->increments('id');
            $t->string('name');
        });

        Schema::create('ticket_statuses', function ($table) {
            $table->increments('id');
            $table->text('name');
        });

        Schema::create('reserved_tickets', function ($table) {
            $table->increments('id');
            $table->integer('ticket_id');
            $table->integer('event_id');
            $table->integer('quantity_reserved');
            $table->datetime('expires');
            $table->string('session_id', 45);
            $table->nullableTimestamps();
        });

        Schema::create('timezones', function ($t) {
            $t->increments('id');
            $t->string('name');
            $t->string('location');
        });

        Schema::create('date_formats', function ($t) {
            $t->increments('id');
            $t->string('format');
            $t->string('picker_format');
            $t->string('label');
        });

        Schema::create('datetime_formats', function ($t) {
            $t->increments('id');
            $t->string('format');
            $t->string('picker_format');
            $t->string('label');
        });

        // Create the `currency` table
        Schema::create('currencies', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('title', 255);
            $table->string('symbol_left', 12);
            $table->string('symbol_right', 12);
            $table->string('code', 3);
            $table->integer('decimal_place');
            $table->double('value', 15, 8);
            $table->string('decimal_point', 3);
            $table->string('thousand_point', 3);
            $table->integer('status');
            $table->nullableTimestamps();
        });

        /*
         * Accounts table
         */
        Schema::create('accounts', function ($t) {
            $t->increments('id');

            $t->string('first_name');
            $t->string('last_name');
            $t->string('email');

            $t->unsignedInteger('timezone_id')->nullable();
            $t->unsignedInteger('date_format_id')->nullable();
            $t->unsignedInteger('datetime_format_id')->nullable();
            $t->unsignedInteger('currency_id')->nullable();
            //$t->unsignedInteger('payment_gateway_id')->default(config('attendize.default_payment_gateway'));

            $t->nullableTimestamps();
            $t->softDeletes();

            $t->string('name')->nullable();
            $t->string('last_ip')->nullable();
            $t->timestamp('last_login_date')->nullable();

            $t->string('address1')->nullable();
            $t->string('address2')->nullable();
            $t->string('city')->nullable();
            $t->string('state')->nullable();
            $t->string('postal_code')->nullable();
            $t->unsignedInteger('country_id')->nullable();
            $t->text('email_footer')->nullable();

            $t->boolean('is_active')->default(false);
            $t->boolean('is_banned')->default(false);
            $t->boolean('is_beta')->default(false);

            $t->string('stripe_access_token', 55)->nullable();
            $t->string('stripe_refresh_token', 55)->nullable();
            $t->string('stripe_secret_key', 55)->nullable();
            $t->string('stripe_publishable_key', 55)->nullable();
            $t->text('stripe_data_raw', 55)->nullable();

            $t->foreign('timezone_id')->references('id')->on('timezones');
            $t->foreign('date_format_id')->references('id')->on('date_formats');
            $t->foreign('datetime_format_id')->references('id')->on('date_formats');
            //$t->foreign('payment_gateway_id')->references('id')->on('payment_gateways');
            $t->foreign('currency_id')->references('id')->on('currencies');
        });

        /*
         * Users Table
         */
        Schema::create('users', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->nullableTimestamps();
            $t->softDeletes();

            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->string('phone')->nullable();
            $t->string('email');
            $t->string('password');
            $t->string('confirmation_code');
            $t->boolean('is_registered')->default(false);
            $t->boolean('is_confirmed')->default(false);
            $t->boolean('is_parent')->default(false);
            $t->string('remember_token', 100)->nullable();

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('organisers', function ($table) {
            $table->increments('id')->index();

            $table->nullableTimestamps();
            $table->softDeletes();

            $table->unsignedInteger('account_id')->index();

            $table->string('name');
            $table->text('about');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('confirmation_key', 20);
            $table->string('facebook');
            $table->string('twitter');
            $table->string('logo_path')->nullable();
            $table->boolean('is_email_confirmed')->default(0);

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('events', function ($t) {
            $t->increments('id');

            $t->string('title');
            $t->string('location')->nullable();
            $t->string('bg_type', 15)->default('color');
            $t->string('bg_color')->default(config('attendize.event_default_bg_color'));
            $t->string('bg_image_path')->nullable();
            $t->text('description');

            $t->dateTime('start_date')->nullable();
            $t->dateTime('end_date')->nullable();

            $t->dateTime('on_sale_date')->nullable();

            $t->integer('account_id')->unsigned()->index();
            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

            $t->integer('user_id')->unsigned();
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $t->unsignedInteger('currency_id')->nullable();
            $t->foreign('currency_id')->references('id')->on('currencies');

            $t->decimal('sales_volume', 13, 2)->default(0);
            $t->decimal('organiser_fees_volume', 13, 2)->default(0);
            $t->decimal('organiser_fee_fixed', 13, 2)->default(0);
            $t->decimal('organiser_fee_percentage', 4, 3)->default(0);
            $t->unsignedInteger('organiser_id');
            $t->foreign('organiser_id')->references('id')->on('organisers');

            $t->string('venue_name');
            $t->string('venue_name_full')->nullable();
            $t->string('location_address', 355)->nullable();
            $t->string('location_address_line_1', 355);
            $t->string('location_address_line_2', 355);
            $t->string('location_country')->nullable();
            $t->string('location_country_code')->nullable();
            $t->string('location_state');
            $t->string('location_post_code');
            $t->string('location_street_number')->nullable();
            $t->string('location_lat')->nullable();
            $t->string('location_long')->nullable();
            $t->string('location_google_place_id')->nullable();

            $t->unsignedInteger('ask_for_all_attendees_info')->default(0);

            $t->text('pre_order_display_message')->nullable();

            $t->text('post_order_display_message')->nullable();

            $t->text('social_share_text')->nullable();
            $t->boolean('social_show_facebook')->default(true);
            $t->boolean('social_show_linkedin')->default(true);
            $t->boolean('social_show_twitter')->default(true);
            $t->boolean('social_show_email')->default(true);
            $t->boolean('social_show_googleplus')->default(true);

            $t->unsignedInteger('location_is_manual')->default(0);

            $t->boolean('is_live')->default(false);

            $t->nullableTimestamps();
            $t->softDeletes();
        });

        /*
         * Users table
         */
        Schema::create('orders', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('order_status_id');
            $t->nullableTimestamps();
            $t->softDeletes();

            $t->string('first_name');
            $t->string('last_name');
            $t->string('email');
            $t->string('ticket_pdf_path', 155)->nullable();

            $t->string('order_reference', 15);
            $t->string('transaction_id', 50)->nullable();

            $t->decimal('discount', 8, 2)->nullable();
            $t->decimal('booking_fee', 8, 2)->nullable();
            $t->decimal('organiser_booking_fee', 8, 2)->nullable();
            $t->date('order_date')->nullable();

            $t->text('notes')->nullable();
            $t->boolean('is_deleted')->default(0);
            $t->boolean('is_cancelled')->default(0);
            $t->boolean('is_partially_refunded')->default(0);
            $t->boolean('is_refunded')->default(0);

            $t->decimal('amount', 13, 2);
            $t->decimal('amount_refunded', 13, 2)->nullable();

            $t->unsignedInteger('event_id')->index();
            $t->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('no action');
        });

        /*
         * Tickets table
         */
        Schema::create('tickets', function ($t) {
            $t->increments('id');
            $t->nullableTimestamps();
            $t->softDeletes();

            $t->unsignedInteger('edited_by_user_id')->nullable();
            $t->unsignedInteger('account_id')->index();
            $t->unsignedInteger('order_id')->nullable();

            $t->unsignedInteger('event_id')->index();
            $t->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $t->string('title');
            $t->text('description');
            $t->decimal('price', 13, 2);

            $t->integer('max_per_person')->nullable()->default(null);
            $t->integer('min_per_person')->nullable()->default(null);

            $t->integer('quantity_available')->nullable()->default(null);
            $t->integer('quantity_sold')->default(0);

            $t->dateTime('start_sale_date')->nullable();
            $t->dateTime('end_sale_date')->nullable();

            $t->decimal('sales_volume', 13, 2)->default(0);
            $t->decimal('organiser_fees_volume', 13, 2)->default(0);

            $t->tinyInteger('is_paused')->default(0);

            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->foreign('order_id')->references('id')->on('orders');
            $t->foreign('edited_by_user_id')->references('id')->on('users');

            $t->unsignedInteger('public_id')->nullable()->index();

            $t->unsignedInteger('user_id');
            $t->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('order_items', function ($table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->integer('quantity');
            $table->decimal('unit_price', 13, 2);
            $table->decimal('unit_booking_fee', 13, 2)->nullable();
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->softDeletes();
        });

        /*
         * checkbox, multiselect, select, radio, text etc.
         */
//        Schema::create('question_types', function($t) {
//            $t->increments('id');
//            $t->string('name');
//            $t->boolean('allow_multiple')->default(FALSE);
//        });
//
//
//        Schema::create('questions', function($t) {
//            $t->nullableTimestamps();
//            $t->softDeletes();
//
//            $t->increments('id');
//
//            $t->string('title', 255);
//            $t->text('instructions');
//            $t->text('options');
//
//
//            $t->unsignedInteger('question_type_id');
//            $t->unsignedInteger('account_id')->index();
//
//            $t->tinyInteger('is_required')->default(0);
//
//
//            /*
//             * If multi select - have question options
//             */
//            $t->foreign('question_type_id')->references('id')->on('question_types');
//            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');$t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
//
//        });
//
//        /**
//         * Related to each question  , can have one or many
//         * Whats you name etc?
//         *
//         */
//        Schema::create('question_options', function($t) {
//            $t->increments('id');
//            $t->string('name');
//            $t->integer('question_id')->unsigned()->index();
//            $t->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
//        });
//
//
//        Schema::create('answers', function($t) {
//            $t->increments('id');
//
//
//            $t->integer('question_id')->unsigned()->index();
//            $t->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
//
//            $t->integer('ticket_id')->unsigned()->index();
//            $t->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
//
//            $t->text('answer');
//        });
//
//
//
//
//        /**
//         * Tickets / Questions pivot table
//         */
//        Schema::create('event_question', function($t) {
//            $t->increments('id');
//            $t->integer('event_id')->unsigned()->index();
//            $t->foreign('event_id')->references('id')->on('event')->onDelete('cascade');
//            $t->integer('question_id')->unsigned()->index();
//            $t->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
//        });
//

        /*
         * Tickets / Orders pivot table
         */
        Schema::create('ticket_order', function ($t) {
            $t->increments('id');
            $t->integer('order_id')->unsigned()->index();
            $t->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $t->integer('ticket_id')->unsigned()->index();
            $t->foreign('ticket_id')->references('id')->on('users')->onDelete('cascade');
        });

        /*
         * Tickets / Questions pivot table
         */
//        Schema::create('ticket_question', function($t) {
//            $t->increments('id');
//            $t->integer('ticket_id')->unsigned()->index();
//            $t->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
//            $t->integer('question_id')->unsigned()->index();
//            $t->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
//        });

        Schema::create('event_stats', function ($table) {
            $table->increments('id')->index();
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('unique_views')->default(0);
            $table->integer('tickets_sold')->default(0);

            $table->decimal('sales_volume', 13, 2)->default(0);
            $table->decimal('organiser_fees_volume', 13, 2)->default(0);

            $table->unsignedInteger('event_id')->index();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('attendees', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('order_id')->index();
            $t->unsignedInteger('event_id')->index();
            $t->unsignedInteger('ticket_id')->index();

            $t->string('first_name');
            $t->string('last_name');
            $t->string('email');

            $t->string('reference', 20);
            $t->integer('private_reference_number')->index();

            $t->nullableTimestamps();
            $t->softDeletes();

            $t->boolean('is_cancelled')->default(false);
            $t->boolean('has_arrived')->default(false);
            $t->dateTime('arrival_time')->nullable();

            $t->unsignedInteger('account_id')->index();
            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

            $t->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $t->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $t->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('messages', function ($table) {
            $table->increments('id');
            $table->text('message');
            $table->string('subject');
            $table->integer('recipients')->nullable(); //ticket_id or null for all
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('is_sent')->default(0);
            $table->dateTime('sent_at')->nullable();
            $table->nullableTimestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
        });

        Schema::create('event_images', function ($t) {
            $t->increments('id');
            $t->string('image_path');
            $t->nullableTimestamps();

            $t->unsignedInteger('event_id');
            $t->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $t->unsignedInteger('account_id');
            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

            $t->unsignedInteger('user_id');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'order_statuses',
            'ticket_statuses',
            'reserved_tickets',
            'timezones',
            'date_formats',
            'datetime_formats',
            'currencies',
            'accounts',
            'users',
            'organisers',
            'events',
            'orders',
            'tickets',
            'order_items',
            'ticket_order',
            'event_stats',
            'attendees',
            'messages',
            'event_images',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            Schema::drop($table);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

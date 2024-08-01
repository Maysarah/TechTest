<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::unprepared('
            CREATE PROCEDURE GetArticleById (
                IN p_article_id INT,
                OUT p_article_title VARCHAR(255),
                OUT p_article_content TEXT
            )
            BEGIN
                -- Initialize the output parameters
                SET p_article_title = "";
                SET p_article_content = "";

                -- Fetch the article details based on the provided article ID
                SELECT title, content
                INTO p_article_title, p_article_content
                FROM articles
                WHERE id = p_article_id;

                -- Handle the case where no rows are found
                IF p_article_title IS NULL THEN
                    SET p_article_title = "Article not found";
                    SET p_article_content = "No content available";
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetArticleById');
    }
};

INSERT INTO `openai_chat_category` (`name`, `short_name`, `slug`, `description`, `role`, `human_name`, `helps_with`,
                                    `prompt_prefix`, `image`, `color`, `created_at`, `updated_at`, `chat_completions`)
VALUES ('PdfAI', 'PI', 'ai_pdf', 'Image PDF Expert', 'PDF Expert', 'PDF AI',
        'I can assist you with PDF or Images-related information or questions', 'As a Pdf AI,', 'assets/img/vision.png',
        '#EDBBBE', '2023-05-16 03:34:57', '2023-05-16 03:39:11',
        '[{"role": "system", "content": "You are a PDF AI assistant."}]');
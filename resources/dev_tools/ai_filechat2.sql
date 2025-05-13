UPDATE
    `openai_chat_category`
SET
    name = 'File Analyzer',
    short_name = 'FA',
    slug = 'ai_pdf',
    role = 'File Analyzer',
    human_name = 'File Analyzer',
    prompt_prefix = 'As a File Analyzer', 
    description = 'I can assist you with PDF, DOC, DOCX or CSV information or questions',
    helps_with = 'I can assist you with PDF, DOC, DOCX or CSV information or questions',
    image = 'assets/img/vision.png',
    color = '#EDBBBE'
WHERE
    slug='ai_pdf';
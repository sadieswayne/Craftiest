INSERT INTO `email_templates` (`title`, `subject`, `content`, `created_at`, `updated_at`) VALUES 
('Team Invite', 'Team Invite Email', '<div style="padding: 0 19px">
    <h1>You are Invited! Congrats!</h1>
    <h1>{site_name}</h1>
    <p>Hey,</p>
    <p>We’re excited to invite you to join {site_name}. It is designed to help businesses and individuals leverage the power of artificial intelligence to generate any kind of content easily.</p>
    <p>You can use {site_name} for: </p>
    <p>
    <ul>
        <li>Copywriting</li>
        <li>Images</li>
        <li>ChatBot</li>
        <li>Speech to Text</li>
        <li>Coding</li>
    </ul>
    </p>
    <p>Once you have created your account, you can start exploring the platform and see for yourself how it can benefit you.</p>
    <p>Thank you for considering this invitation. I look forward to seeing you on {site_name}.</p>
</div>

<br>

<a href="{register_url}" class="btn btn-lg btn-block btn-round">
    Discover {site_name}
</a>

<p class="need-help-p">Need help? <a href="{site_url}">Contact us.</a></p>', null, '2024-02-06 10:37:15');

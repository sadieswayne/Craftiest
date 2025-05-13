<?php

namespace App\Http\Controllers;

use App\Models\UserOpenaiChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class ExportChatController extends Controller
{
    public function generatePdf(Request $request)
    {

        $messages = UserOpenaiChatMessage::where('user_openai_chat_id', $request->id)->get();

        // for test
        return view('panel.admin.openai.chat.document.pdf', ['messages' => $messages]);

        $pdf = PDF::loadView('panel.admin.openai.chat.document.pdf', ['messages' => $messages]);

        return $pdf->download('document.pdf');
    }

    public function generateWord(Request $request)
    {
        $messages = UserOpenaiChatMessage::where('user_openai_chat_id', $request->id)->get();

        $phpWord = new PhpWord;

        $bladeView = view('panel.admin.openai.chat.document.pdf', compact('messages'))->render();

        $section = $phpWord->addSection();
        // \PhpOffice\PhpWord\Shared\Html::addHtml($section, $bladeView, true);
        $bladeView = preg_replace('~<(/?\d)~', '<number$1', $bladeView);
        Html::addHtml($section, $bladeView);

        $filename = 'document.docx';
        $phpWord->save($filename);

        // Return the Word document as a response
        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function generateTxt(Request $request)
    {
        $messages = UserOpenaiChatMessage::where('user_openai_chat_id', $request->id)->get();

        $fileContents = '';
        foreach ($messages as $message) {
            if ($message->input != null) {
                $fileContents .= 'You: ';
                $fileContents .= $message->input;
                $fileContents .= "\n";
            }
            if ($message->output != null) {
                $fileContents .= 'Chatbot: ';
                $fileContents .= $message->output;
                $fileContents .= "\n";
            }
        }
        $fileName = 'document.txt';

        // Save the text file to the storage disk
        Storage::disk('local')->put($fileName, $fileContents);

        // Get the full path to the text file
        $filePath = Storage::disk('local')->path($fileName);

        // Create a response to download the file
        return response()->download($filePath, $fileName);
    }
}

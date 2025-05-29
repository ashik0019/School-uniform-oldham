<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Ticket;
use App\User;
use Auth;
use App\TicketReply;
use App\Mail\SupportMailManager;
use Mail;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd('sss');
        $tickets = Ticket::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        if ($tickets){
            return response()->json(['success'=> true, 'response' => $tickets], 200);
        }else{
            return response()->json(['success'=> false, 'response' => "Something Went wrong!"], 401);
        }

    }

    public function seller_store(Request $request)
    {
        $ticket_reply = new TicketReply();
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = $request->user_id;
        $ticket_reply->reply = $request->reply;

        $files = array();

        if($request->hasFile('attachments')){
            foreach ($request->attachments as $key => $attachment) {
                $item['name'] = $attachment->getClientOriginalName();
                $item['path'] = $attachment->store('uploads/support_tickets/');
                array_push($files, $item);
            }
            $ticket_reply->files = json_encode($files);
        }

        $ticket_reply->ticket->viewed = 0;
        $ticket_reply->ticket->status = 'pending';
        $ticket_reply->ticket->save();
        if($ticket_reply->save()){

            return response()->json(['success'=> true, 'response' => $ticket_reply], 200);
        }
        else{
            return response()->json(['success'=> false, 'response' => "Something Went wrong!"], 401);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // dd($request->all());
        $ticket = new Ticket;
        $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
        $ticket->user_id = Auth::user()->id;
        $ticket->subject = $request->subject;
        $ticket->details = $request->details;

        $files = array();

        if($request->hasFile('attachments')){
            foreach ($request->attachments as $key => $attachment) {
                $item['name'] = $attachment->getClientOriginalName();
                $item['path'] = $attachment->store('uploads/support_tickets/');
                array_push($files, $item);
            }
            $ticket->files = json_encode($files);
        }

        if($ticket->save()){
            $this->send_support_mail_to_admin($ticket);
            return response()->json(['success'=> true, 'response' => 'Ticket Successfully Genereted!!'], 200);
        }
        else{
            return response()->json(['success'=> false, 'response' => 'Something went wrong.'], 401);
        }


    }

    public function send_support_mail_to_admin($ticket){
        $array['view'] = 'emails.support';
        $array['subject'] = 'Support ticket Code is:- '.$ticket->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = 'Hi. A ticket has been created. Please check the ticket.';
        $array['link'] = route('support_ticket.admin_show', encrypt($ticket->id));
        $array['sender'] = $ticket->user->name;
        $array['details'] = $ticket->details;

        // dd($array);
        // dd(User::where('user_type', 'admin')->first()->email);
        try {
            Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
    }

    public function send_support_reply_email_to_user($ticket, $tkt_reply){
        $array['view'] = 'emails.support';
        $array['subject'] = 'Support ticket Code is:- '.$ticket->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = 'Hi. A ticket has been created. Please check the ticket.';
        $array['link'] = route('support_ticket.show', encrypt($ticket->id));
        $array['sender'] = $tkt_reply->user->name;
        $array['details'] = $tkt_reply->reply;

        // dd($array);
        // dd(User::where('user_type', 'admin')->first()->email);
        try {
            Mail::to($ticket->user->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }
    }

    public function admin_store(Request $request)
    {
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = Auth::user()->id;
        $ticket_reply->reply = $request->reply;

        $files = array();

        if($request->hasFile('attachments')){
            foreach ($request->attachments as $key => $attachment) {
                $item['name'] = $attachment->getClientOriginalName();
                $item['path'] = $attachment->store('uploads/support_tickets/');
                array_push($files, $item);
            }
            $ticket_reply->files = json_encode($files);
        }

        $ticket_reply->ticket->client_viewed = 0;
        $ticket_reply->ticket->status = $request->status;
        $ticket_reply->ticket->save();


        if($ticket_reply->save()){
            flash(translate('Reply has been sent successfully'))->success();
            $this->send_support_reply_email_to_user($ticket_reply->ticket, $ticket_reply);
            return back();
        }
        else{
            flash(translate('Something went wrong'))->error();
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->client_viewed = 1;
        $ticket->save();
        $ticket_replies = $ticket->ticketreplies;
        if ($ticket){
            $data['ticket'] = $ticket;
            $data['ticket_replies'] = $ticket_replies;
            return response()->json(['success'=> true, 'response' => $data], 200);
        }else{
            return response()->json(['success'=> true, 'response' => "Something Went wrong!"], 401);
        }
        //return view('frontend.support_ticket.show', compact('ticket','ticket_replies'));
    }

    public function admin_show($id)
    {
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->viewed = 1;
        $ticket->save();
        return view('support_tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

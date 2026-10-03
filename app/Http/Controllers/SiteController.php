<?php

namespace App\Http\Controllers;

use App\Domain\Ops\PlatformSettings;
use App\Domain\Ops\Settings;
use App\Models\ContactMessage;
use App\Models\Plan;
use App\Models\Superadmin;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SiteController extends Controller
{
    public const PAGES = ['home' => '/', 'web-design' => '/web-design', 'your-app' => '/your-app', 'pricing' => '/pricing', 'demo' => '/demo', 'contact' => '/contact', 'privacy' => '/privacy', 'terms' => '/terms'];

    public function home()
    {
        return view('marketing.home', ['fromPrice' => $this->fromPrice(), 'videoId' => PlatformSettings::introVideoId()]);
    }

    public function webDesign()
    {
        return view('marketing.web-design');
    }

    public function yourApp()
    {
        return view('marketing.your-app', ['fromPrice' => $this->fromPrice()]);
    }

    public function pricing()
    {
        return view('marketing.pricing', [
            'plans' => Plan::public()->get(),
            'signups' => PlatformSettings::signupsEnabled(),
        ]);
    }

    public function demo()
    {
        $url = url('/demo-barber');
        $qr = (new Writer(new ImageRenderer(new RendererStyle(180, 1), new SvgImageBackEnd())))->writeString($url);

        return view('marketing.demo', ['demoUrl' => $url, 'qr' => $qr]);
    }

    public function contact()
    {
        return view('marketing.contact');
    }

    public function sendContact(Request $request)
    {
        if ($request->filled('company_site')) {
            return redirect()->route('contact')->with('status', 'Thanks! We got your message.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:40',
            'service' => 'required|in:'.implode(',', array_keys(ContactMessage::SERVICES)),
            'message' => 'required|string|min:5|max:3000',
        ]);

        $message = ContactMessage::create($data + ['ip' => $request->ip(), 'user_agent' => substr((string) $request->userAgent(), 0, 255)]);

        try {
            $to = Settings::get('support_email') ?: Superadmin::value('email');

            if ($to) {
                Mail::raw("New message from {$message->name} <{$message->email}>\nPhone: {$message->phone}\nInterested in: ".ContactMessage::SERVICES[$message->service]."\n\n{$message->message}", fn ($m) => $m->to($to)->replyTo($message->email, $message->name)->subject('New enquiry from '.$message->name));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('contact')->with('status', 'Thanks! We got your message and will reply by email.');
    }

    public function privacy()
    {
        return view('marketing.privacy');
    }

    public function terms()
    {
        return view('marketing.terms');
    }

    public function sitemap()
    {
        $urls = collect(array_slice(self::PAGES, 0, 6))->map(fn ($path) => url($path))->all();

        return response()->view('marketing.sitemap', compact('urls'), 200, ['Content-Type' => 'application/xml']);
    }

    private function fromPrice(): ?Plan
    {
        return Plan::public()->where('price_cents', '>', 0)->first();
    }
}

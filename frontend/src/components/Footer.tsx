"use client";

import Link from "next/link";
import { ArrowRight, ChevronUp, ShieldCheck, Truck, RefreshCw } from "lucide-react";
import { useState } from "react";
import { useSettings } from "@/hooks/useSettings";
import { getAssetUrl } from "@/lib/imageHelper";
import { motion } from "framer-motion";

export default function Footer() {
  const [whatsappPhone, setWhatsappPhone] = useState("");
  const { data: settings } = useSettings();

  const whatsappNum = settings?.web_whatsapp_number 
    ? settings.web_whatsapp_number.replace(/\D/g, '') 
    : "923001234567";

  const handleWhatsappCommunitySubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (whatsappPhone.trim()) {
      const msg = encodeURIComponent(`Hi TrendHub! I would like to join the TrendHub VIP WhatsApp Community. My phone number is: ${whatsappPhone}`);
      window.open(`https://wa.me/${whatsappNum}?text=${msg}`, "_blank");
      setWhatsappPhone("");
    }
  };

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  return (
    <footer className="w-full bg-[#080808] text-white border-t border-white/10 pt-16 pb-10 font-sans select-none relative overflow-hidden">
      <div className="max-w-[1400px] mx-auto px-6 space-y-16">
        
        {/* 1. TOP JOIN OUR WHATSAPP COMMUNITY VIP BANNER */}
        <motion.div 
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-950/50 via-neutral-950 to-neutral-900 border border-emerald-500/30 p-8 sm:p-12 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-8"
        >
          <div className="space-y-2 text-center lg:text-left max-w-[600px]">
            <span className="text-[10px] font-sans font-bold uppercase tracking-[0.3em] text-emerald-400 flex items-center justify-center lg:justify-start gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              JOIN OUR WHATSAPP COMMUNITY
            </span>
            <h3 className="font-serif text-2xl sm:text-4xl tracking-wide font-normal text-white uppercase">
              Get VIP Drops & Flash Sales On WhatsApp
            </h3>
            <p className="text-xs sm:text-sm text-neutral-400 font-sans font-light leading-relaxed">
              Enter your WhatsApp number below to join our exclusive VIP group for instant drop alerts, secret promo codes, and private sales.
            </p>
          </div>

          <form onSubmit={handleWhatsappCommunitySubmit} className="w-full lg:w-auto flex flex-col sm:flex-row gap-3 min-w-[320px] sm:min-w-[440px]">
            <input
              type="tel"
              placeholder="ENTER YOUR WHATSAPP NUMBER (+92...)"
              value={whatsappPhone}
              onChange={(e) => setWhatsappPhone(e.target.value)}
              className="bg-black/60 border border-emerald-500/35 rounded-md px-5 py-3.5 text-xs tracking-wider uppercase text-white placeholder-neutral-500 focus:outline-none focus:border-emerald-400 transition-all flex-grow shadow-inner"
              required
            />
            <button
              type="submit"
              className="bg-emerald-500 hover:bg-emerald-400 text-white text-xs uppercase tracking-[0.2em] font-sans font-bold px-8 py-3.5 rounded-md transition-all duration-300 shadow-lg shrink-0 cursor-pointer flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95"
            >
              <span>JOIN COMMUNITY</span>
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
              </svg>
            </button>
          </form>
        </motion.div>

        {/* 2. VALUE PROPOSITION TRIPLE BADGES WITH ANIMATIONS */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.1 }}
          className="grid grid-cols-1 sm:grid-cols-3 gap-6 py-6 border-y border-white/10 text-center"
        >
          <div className="flex items-center justify-center gap-3 p-4 bg-white/5 rounded-xl border border-white/5 hover:border-indigo-500/40 hover:bg-white/10 transition-all duration-300">
            <Truck className="text-indigo-400 shrink-0" size={22} />
            <div className="text-left">
              <h5 className="text-xs font-bold uppercase tracking-wider text-white">Nationwide Shipping</h5>
              <p className="text-[11px] text-neutral-400">Fast delivery across Pakistan</p>
            </div>
          </div>

          <div className="flex items-center justify-center gap-3 p-4 bg-white/5 rounded-xl border border-white/5 hover:border-indigo-500/40 hover:bg-white/10 transition-all duration-300">
            <RefreshCw className="text-indigo-400 shrink-0" size={22} />
            <div className="text-left">
              <h5 className="text-xs font-bold uppercase tracking-wider text-white">Hassle-Free Exchange</h5>
              <p className="text-[11px] text-neutral-400">7-Day easy return & exchange</p>
            </div>
          </div>

          <div className="flex items-center justify-center gap-3 p-4 bg-white/5 rounded-xl border border-white/5 hover:border-indigo-500/40 hover:bg-white/10 transition-all duration-300">
            <ShieldCheck className="text-indigo-400 shrink-0" size={22} />
            <div className="text-left">
              <h5 className="text-xs font-bold uppercase tracking-wider text-white">100% Authentic Quality</h5>
              <p className="text-[11px] text-neutral-400">Premium cotton & linen fabrics</p>
            </div>
          </div>
        </motion.div>

        {/* 3. MAIN 4-COLUMN FOOTER CONTENT */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.2 }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pt-4"
        >
          
          {/* Column 1: Brand & Philosophy & Social Links */}
          <div className="space-y-6">
            <Link href="/" className="inline-block">
              {settings?.web_site_logo ? (
                <img 
                  src={getAssetUrl(settings.web_site_logo)} 
                  alt={settings?.web_site_name || "TrendHub Store"} 
                  className="h-10 sm:h-12 object-contain filter brightness-100"
                />
              ) : (
                <h4 className="font-serif text-2xl tracking-[0.25em] font-bold uppercase text-white">
                  {settings?.web_site_name || "TRENDHUB STORE"}
                </h4>
              )}
            </Link>

            <p className="text-xs text-neutral-400 leading-relaxed font-sans max-w-[340px]">
              {settings?.web_about_us || "TrendHub Menswear is dedicated to minimal luxury, precision tailoring, and timeless elegance for modern gentlemen."}
            </p>

            {/* Live WhatsApp Customer Support Badge */}
            <a
              href={`https://wa.me/${whatsappNum}`}
              target="_blank"
              rel="noreferrer"
              className="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/30 hover:bg-emerald-500 hover:text-white transition-all text-emerald-400 text-xs font-sans font-semibold group shadow-md"
            >
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse group-hover:bg-white" />
              <span>WhatsApp Live Support</span>
            </a>

            {/* Social Icons (Instagram, Facebook, TikTok, WhatsApp) */}
            <div className="flex items-center gap-3 pt-2">
              {settings?.web_instagram_link && (
                <a 
                  href={settings.web_instagram_link} 
                  target="_blank" 
                  rel="noreferrer" 
                  className="w-10 h-10 rounded-full bg-white/5 border border-white/15 flex items-center justify-center hover:bg-white hover:text-black transition-all text-gray-300 shadow-md hover:scale-110"
                  title="Instagram"
                >
                  <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </a>
              )}
              {settings?.web_facebook_link && (
                <a 
                  href={settings.web_facebook_link} 
                  target="_blank" 
                  rel="noreferrer" 
                  className="w-10 h-10 rounded-full bg-white/5 border border-white/15 flex items-center justify-center hover:bg-white hover:text-black transition-all text-gray-300 shadow-md hover:scale-110"
                  title="Facebook"
                >
                  <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                </a>
              )}
              {settings?.web_tiktok_link && (
                <a 
                  href={settings.web_tiktok_link} 
                  target="_blank" 
                  rel="noreferrer" 
                  className="w-10 h-10 rounded-full bg-white/5 border border-white/15 flex items-center justify-center hover:bg-black hover:text-white hover:border-pink-500 transition-all text-gray-300 shadow-md hover:scale-110"
                  title="TikTok"
                >
                  <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 1 1-5.2-1.74 2.89 2.89 0 0 1 2.31-2.84V7.59a6.34 6.34 0 0 0-5.11 6.2 6.34 6.34 0 1 0 11.45-3.69V8.46a8.27 8.27 0 0 0 3.77 1.68V6.69z"/>
                  </svg>
                </a>
              )}
              <a 
                href={`https://wa.me/${whatsappNum}`} 
                target="_blank" 
                rel="noreferrer" 
                className="w-10 h-10 rounded-full bg-white/5 border border-white/15 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-gray-300 shadow-md hover:scale-110"
                title="WhatsApp"
              >
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
              </a>
            </div>
          </div>

          {/* Column 2: Menswear Collections */}
          <div className="space-y-4">
            <h5 className="text-[11px] font-bold uppercase tracking-[0.25em] text-indigo-400">Menswear Collections</h5>
            <ul className="space-y-3 text-xs text-neutral-300">
              <li>
                <Link href="/shop?category=Shirts" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Formal & Oxford Shirts</Link>
              </li>
              <li>
                <Link href="/shop?category=Trousers" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Chino Trousers & Pants</Link>
              </li>
              <li>
                <Link href="/shop?category=Polos" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Essential Pima Cotton Polos</Link>
              </li>
              <li>
                <Link href="/shop?category=Shorts" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Tailored Casual Shorts</Link>
              </li>
              <li>
                <Link href="/shop?category=Suits" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Italian Suits & Blazers</Link>
              </li>
            </ul>
          </div>

          {/* Column 3: Customer Care & Links */}
          <div className="space-y-4">
            <h5 className="text-[11px] font-bold uppercase tracking-[0.25em] text-indigo-400">Customer Assistance</h5>
            <ul className="space-y-3 text-xs text-neutral-300">
              <li>
                <Link href="/store-locator" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Find a Flagship Store</Link>
              </li>
              <li>
                <Link href="/checkout" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">Track Order & Delivery</Link>
              </li>
              <li>
                <Link href="/wishlist" className="hover:text-white hover:translate-x-1 inline-block transition-all duration-200">My Saved Favorites</Link>
              </li>
              <li>
                <span className="text-neutral-400 cursor-default">7-Day Returns & Exchanges</span>
              </li>
              <li>
                <span className="text-neutral-400 cursor-default">Size & Tailoring Guide</span>
              </li>
            </ul>
          </div>

          {/* Column 4: Outlets & Hours */}
          <div className="space-y-4">
            <h5 className="text-[11px] font-bold uppercase tracking-[0.25em] text-indigo-400">Store Hours & Contact</h5>
            <div className="space-y-2 text-xs text-neutral-400 font-sans">
              <p className="text-white font-medium">Flagship Store Hours:</p>
              <p>Monday - Sunday: 11:00 AM - 10:00 PM</p>
              <p className="pt-2 text-white font-medium">Customer Support Email:</p>
              <p className="text-indigo-300">{settings?.web_contact_email || "support@trendhubstore.pk"}</p>
            </div>

            <div className="pt-2">
              <span className="text-[10px] font-bold uppercase tracking-widest text-neutral-500 block mb-2">Accepted Payment Methods</span>
              <div className="flex flex-wrap gap-2 text-[9px] uppercase tracking-wider font-bold text-neutral-300">
                <span className="px-2 py-1 bg-white/10 rounded border border-white/10">Cash On Delivery</span>
                <span className="px-2 py-1 bg-white/10 rounded border border-white/10">EasyPaisa</span>
                <span className="px-2 py-1 bg-white/10 rounded border border-white/10">Bank Transfer</span>
              </div>
            </div>
          </div>

        </motion.div>

        {/* 4. BOTTOM FOOTER BAR WITH SCROLL TOP BUTTON */}
        <div className="border-t border-white/10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-sans text-neutral-400">
          <span>
            &copy; {new Date().getFullYear()} TrendHub Store. All Rights Reserved.
          </span>

          <span className="tracking-widest uppercase text-[10px] text-neutral-500">
            Crafted for the Modern Tastemaker
          </span>

          <button
            onClick={scrollToTop}
            className="flex items-center gap-2 hover:text-white transition-all duration-300 cursor-pointer text-xs uppercase tracking-widest text-neutral-300 bg-white/5 border border-white/10 px-4 py-2 rounded-full hover:bg-white/20 hover:scale-105 active:scale-95 shadow-md"
          >
            <span>Back to top</span>
            <ChevronUp size={16} />
          </button>
        </div>

      </div>
    </footer>
  );
}




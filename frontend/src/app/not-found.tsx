import Link from 'next/link';

export default function NotFound() {
  return (
    <div className="min-h-[70vh] flex flex-col items-center justify-center px-6 text-center font-sans select-none bg-white">
      <div className="space-y-4 max-w-md">
        <span className="text-[10px] tracking-[0.3em] uppercase text-gray-400 font-bold block">
          Error 404
        </span>
        <h2 className="font-serif text-3xl sm:text-4xl uppercase tracking-[0.2em] font-light text-black">
          Page Not Found
        </h2>
        <p className="text-xs sm:text-sm text-gray-500 font-light leading-relaxed">
          The collection or page you are looking for does not exist or has been moved.
        </p>
        <div className="pt-6">
          <Link
            href="/"
            className="inline-block bg-black text-white px-8 py-3.5 text-xs uppercase tracking-[0.2em] font-bold hover:bg-neutral-800 transition-colors"
          >
            Back to Storefront
          </Link>
        </div>
      </div>
    </div>
  );
}

"use client";

import Link from "next/link";

export default function TopBar() {
    return (
        <nav className="bg-gray-800 text-white px-6 py-4 shadow-lg">
            <div className="max-w-7xl mx-auto flex items-center justify-between">
                <div className="text-2xl font-bold">🎵 My Jukebox</div>

                <div className="space-x-4">
                    <Link href="/" className="hover:text-blue-300">
                        Home
                    </Link>
                    <Link href="/jukebox" className="hover:text-blue-300">
                        Sua Jukebox
                    </Link>
                </div>
            </div>
        </nav>
    );
}

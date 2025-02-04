"use client";

import {useRouter} from "next/navigation";

export default function Home() {
  const router = useRouter();
  router.push('/login');

  return (
      <div className="flex min-h-screen items-center justify-center">
        <span class="text-gray-800">Espere o redirecionamento da tela ou então <a class="text-blue-700 font-bold" href="/login">aperte aqui</a></span>
      </div>
  );
}

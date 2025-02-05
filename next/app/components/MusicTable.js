"use client";

export default function MusicTable({ musics, actions }) {
    return (
        <table className="w-full mt-5 text-left border-collapse border border-gray-300 bg-white shadow-sm rounded-lg">
            <thead className="bg-gray-200">
            <tr>
                <th className="px-4 py-2 border border-gray-300 font-bold">Título</th>
                <th className="px-4 py-2 border border-gray-300 font-bold">Artista</th>
                <th className="px-4 py-2 border border-gray-300 font-bold">Álbum</th>
                <th className="px-4 py-2 border border-gray-300 font-bold">Plataforma</th>
                <th className="px-4 py-2 border border-gray-300 font-bold">Duração</th>
                <th className="px-4 py-2 border border-gray-300 font-bold">Link</th>
                {actions && <th className="px-4 py-2 border border-gray-300 font-bold">Ações</th>}
            </tr>
            </thead>
            <tbody>
            {musics.map((music) => (
                <tr key={music.id} className="hover:bg-gray-100">
                    <td className="px-4 py-2 border border-gray-300">{music.title}</td>
                    <td className="px-4 py-2 border border-gray-300">
                        {music.artists || 'Desconhecido'}
                    </td>
                    <td className="px-4 py-2 border border-gray-300">{music.album_name || 'Desconhecido'}</td>
                    <td className="px-4 py-2 border border-gray-300">{music.platform_name || 'Desconhecida'}</td>
                    <td className="px-4 py-2 border border-gray-300">{music.duration}</td>
                    <td className="px-4 py-2 border border-gray-300">
                        <a
                            href={music.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-blue-600 hover:underline"
                        >
                            Ouvir
                        </a>
                    </td>
                    {actions && (
                        <td className="px-4 py-2 border border-gray-300">
                            {actions(music)}
                        </td>
                    )}
                </tr>
            ))}
            </tbody>
        </table>
    );
}

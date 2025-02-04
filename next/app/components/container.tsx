export default function Container({ children }) {
  return (
      <div className="bg-gray-50 min-h-screen flex items-center justify-center">
        {children}
      </div>
  );
}


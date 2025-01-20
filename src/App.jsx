import Header from './components/header.jsx'
import React, { useEffect, useState } from 'react';
import { api } from "./utils/config";

function App() {
  const [data, setData] = useState(null);

  useEffect(() => {
    fetch(`${api}/data`)
      .then((response) => response.json())
      .then((data) => setData(data))
      .catch((error) => console.error('Error fetching data:', error));
  }, []);

  return (
    <>
      <Header />
      <h1>WordPress Data</h1>
      {data ? (
        <div>
          <h2>{data.site_title}</h2>
          <p>{data.message}</p>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </>
  )
}

export default App

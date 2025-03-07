import {Link} from "react-router-dom";
import Auth from "@/api/auth.js";
import {createRef} from "react";
import {useStateContext} from "../context/ContextProvider.jsx";
import { useState, useEffect } from "react";

export default function Login() {
  const emailRef = createRef()
  const passwordRef = createRef()
  const { setUser, setToken } = useStateContext()
  const [message, setMessage] = useState(null)
  const [loginUrl, setLoginUrl] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    setLoading(true)
    fetch('http://api.local.com/api/auth', {
        headers : {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then((response) => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Something went wrong!');
        })
        .then((data) => setLoginUrl( data.url ))
        .catch((error) => console.error(error))
        .finally(()=> setLoading(false));
}, []);

  const onSubmit = ev => {
    ev.preventDefault()

    const payload = {
      email: emailRef.current.value,
      password: passwordRef.current.value,
    }

    Auth.login(payload)
      .then(({data}) => {
        setUser(data.user)
        setToken(data.token);
      })
      .catch((err) => {
        const response = err.response;
        if (response && response.status === 422) {
          setMessage(response.data.message)
        }
      })
  }

  return (
    <div className="login-signup-form animated fadeInDown">
      <div className="form">
        <form onSubmit={onSubmit}>
          <h1 className="title">Login into your account</h1>

          {message &&
            <div className="alert">
              <p>{message}</p>
            </div>
          }

          <input ref={emailRef} type="email" placeholder="Email"/>
          <input ref={passwordRef} type="password" placeholder="Password"/>
          <button className="btn btn-block">Login</button>
          <p className="message">Not registered? <Link to="/signup">Create an account</Link></p>
          <div className="google-button">
            <a className="btn btn-google" href={loading ? null : loginUrl} >
              <img src="public/icons/google-48.png" alt="Logo" className="logo" width="24" style={{ marginRight: "8px" }} />
              Google Sign In
            </a>
          </div>
        </form>
      </div>
    </div>
  )
}

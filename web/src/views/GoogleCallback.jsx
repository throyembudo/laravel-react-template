import {useEffect} from 'react';
import {useLocation} from "react-router-dom";
import {useStateContext} from "../context/ContextProvider.jsx";

function GoogleCallback() 
{
    const location = useLocation();
    const { setUser, setToken, token } = useStateContext()

    useEffect(() => {
        fetch(`http://api.local.com/api/auth/callback${location.search}`, {
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (!data['error'] && !token) {
                setUser(data.user)
                setToken(data.token);
            }
        })
    }, []);
}

export default GoogleCallback;

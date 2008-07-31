import java.util.Vector;
import helma.xmlrpc.*;

public class Undisposable {

    // The location of our server.
    private static String server_url =
        "http://www.undisposable.org/services/xmlrpc/";

    public static boolean isDisposableEmail (String email) {
        try {

            server_url += "isDisposableEmail/index.php"

            // Create an object to represent our server.
            XmlRpcClient server = new XmlRpcClient(server_url);

            // Build our parameter list.
            Vector params = new Vector();
            params.addElement(email);

            // Call the server, and get our result.
            boolean result =
                (boolean) server.execute("isDisposableEmail", params);

            return result;

        } catch (XmlRpcException exception) {
            /* System.err.println("JavaClient: XML-RPC Fault #" +
                               Integer.toString(exception.code) + ": " +
                               exception.toString());
            */
            return false;
        } catch (Exception exception) {
            // System.err.println("JavaClient: " + exception.toString());
            return false;
        }
    }
}

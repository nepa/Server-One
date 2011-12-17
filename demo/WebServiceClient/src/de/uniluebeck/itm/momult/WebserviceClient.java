package de.uniluebeck.itm.momult;

import org.ksoap2.SoapEnvelope;
import org.ksoap2.serialization.SoapObject;
import org.ksoap2.serialization.SoapSerializationEnvelope;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.TextView;
import org.ksoap2.transport.HttpTransportSE;

public class WebserviceClient extends Activity
{
  /** Webservice namespace */
  private static final String NAMESPACE = "http://www.webserviceX.NET/";
  
  /** Location of webservice description */
  private static final String URL = "http://www.webservicex.net/stockquote.asmx?WSDL";

  /** Webservice method */
  private static final String METHOD_NAME = "GetQuote";
  
  /** Webservice action */
  private static final String SOAP_ACTION = "http://www.webserviceX.NET/GetQuote";

  /**
   * Called when the activity is first created.
   */
  @Override
  public void onCreate(Bundle icicle)
  {
    super.onCreate(icicle);

    setTheme(android.R.style.Theme_Black);
    setContentView(R.layout.main);

    // Add OnClickListener to button
    findViewById(R.id.cmdCallWebservice).setOnClickListener(new OnClickListener()
    {
      public void onClick(View view)
      {
        ((TextView)findViewById(R.id.lblStatus)).setText("Calling webservice...");

        // Arguments that are sent to the webservice
        String symbol = ((EditText)findViewById(R.id.symbol)).getText().toString();
        String tagName = ((EditText)findViewById(R.id.tagName)).getText().toString();

        // Create request object and add argument
        SoapObject request = new SoapObject(NAMESPACE, METHOD_NAME);
        request.addProperty("symbol", symbol);

        // Create SOAP envelope
        SoapSerializationEnvelope envelope = new SoapSerializationEnvelope(SoapEnvelope.VER11);

        envelope.dotNet = true; // If webservice runs on Microsoft .NET
        envelope.setOutputSoapObject(request);

        HttpTransportSE androidHttpTransport = new HttpTransportSE(URL);
        try
        {
          // Call remote method and retrieve response
          androidHttpTransport.call(SOAP_ACTION, envelope);
          SoapObject response = (SoapObject)envelope.bodyIn;
          String xmlDocument = response.getProperty(0).toString();

          // Print content of desired XML tag plus entire SOAP response
          ((TextView)findViewById(R.id.lblStatus)).setText("Response: "
                  + this.getValueOfTag(tagName, xmlDocument) + "\n\n" + xmlDocument);
        }
        catch (Exception e)
        {
          ((TextView)findViewById(R.id.lblStatus)).setText("ERROR:" + e.getClass().getName() + ": " + e.getMessage());
        }
      }

      /**
       * As kSOAP lacks proper XML parsing, this rather hackish method
       * is used to extract the content of single XML tags from the
       * SOAP response.
       */
      private String getValueOfTag(final String tagName, final String response)
      {
        return response.substring(
                response.indexOf("<" + tagName + ">") + tagName.length() + 2,
                response.indexOf("</" + tagName + ">", response.indexOf("<" + tagName + ">")));
      }
    });
  }
}
